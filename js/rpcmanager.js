const DiscordRPC = require('discord-rpc');
var ETCarsClient = require('./etcars.js');
var fetch = require('node-fetch');
var util = require('util');
var config = require('./config');

class RichPresenceManager {
    constructor() {
        this.etcars = new ETCarsClient();

        // setting initial variables state
        this.rpc = null;
        this.mpCheckerIntervalTime = config.mpCheckerIntervalMilliseconds;
        this.locationCheckerIntervalTime = config.locationCheckerIntervalMilliseconds;
        this.mpStatsCheckerIntervalTime = config.mpStatsCheckerIntervalMilliseconds;

        this.mpInfo = null;
        this.lastData = null;
        this.rpcReady = false;
        this.rpcOnChangingState = false;
        this.mpCheckerInterval = null;
        this.mpStatsCheckerInterval = null;
        this.locationCheckerInterval = null;
        this.locationInfo = null;
    }

    onData(data) {
        var instance = this;
        console.log("data recieve something idk")
            //use a try / catch as sometimes the data isn't there when first connecting...plus it's json parsing...

        // putting apart last data received
        instance.lastData = data;
        if (typeof(data.telemetry) != 'undefined' && data.telemetry) {

            // telemetry exists

            // begin to initialize Discord RPC
            // checking if is in valid state
            if (!instance.rpcOnChangingState) {

                // checking if is not ready
                if (!instance.rpcReady) {

                    instance.rpcOnChangingState = true;

                    // getting application id (default ETS2)
                    var applicationID = "559459205560533002";
                    instance.timestamp = Date.now()

                    if (!instance.checkIfMultiplayer(data)) {
                        instance.startLocationChecker();
                    }

                    // creating a new Discord RPC Client instance
                    instance.rpc = new DiscordRPC.Client({
                        transport: 'ipc'
                    });

                    // login to RPC
                    instance.rpc.login({ clientId: applicationID }).then(() => {
                        console.log('Discord RPC ready');
                        // cleaning up variables to save RPC Client state
                        instance.rpcReady = true;
                        instance.rpcOnChangingState = false;
                    }).catch(console.error);
                }
            }

            if (instance.rpcReady) {

                // checking if playing in multiplayer and loading online state, server and position
                if (instance.checkIfMultiplayer(data) && instance.mpInfo == null) {
                    console.log('Multiplayer detected');
                    instance.checkMpInfo();
                    instance.startMPChecker();
                    instance.startMPStatsChecker();
                }

                var activity = instance.buildActivity(data);

                if (activity != null) {
                    instance.rpc.setActivity(activity);
                }
            }
        }
    }

    disableRPC() {
        this.resetETCarsData();
        this.destroyRPCClient();
        this.resetMPChecker();
        this.resetMPStatsChecker();
        this.resetLocationChecker();
    }

    buildActivity(data) {
        var activity = null;

        if (typeof data.telemetry != 'undefined' && data.telemetry) {
            activity = {};

            activity.smallImageText = `${data.telemetry.truck.make} ${data.telemetry.truck.model} - ${this.calculateDistance(data.telemetry.truck.odometer, this.isAts(data))} ${this.getDistanceUnit(this.isAts(data))}`;

            if (config.supportedBrands.includes(data.telemetry.truck.makeID.toLowerCase())) {
                activity.smallImageKey = `${data.telemetry.truck.makeID}`;
            }

            activity.details = '';
            activity.state = '';
            activity.startTimestamp = this.timestamp;

            if (typeof data.telemetry.job != 'undefined' && data.telemetry.job && data.telemetry.job.onJob === true) {
                if (data.telemetry.job.sourceCity != null) {
                    activity.details += `🚚 ${data.telemetry.job.sourceCity} > ${data.telemetry.job.destinationCity} | ${data.telemetry.job.cargo}`;
                } else {
                    activity.details += `🚧 Special Transport | ${data.telemetry.job.cargo}`
                }
                activity.largeImageText = `www.falconites.com`;
            } else {
                if (data.telemetry.truck.make == false) {
                    activity.details += `🕗 Loading game...`
                } else {
                    activity.details += `🚛 Freeroaming | ${this.isAts(data) ? "ATS" : "ETS2"}`;
                }
                activity.largeImageText = `www.falconites.com`;
            }

            activity.largeImageKey = "ets2";

            if (this.mpStatsInfo != null) {
                if (this.mpStatsInfo.prefix != null) {
                    this.mpPrefix = this.mpStatsInfo.prefix;
                } else {
                    this.mpPrefix = '';
                }
            }

            if (this.mpInfo != null && this.mpStatsInfo != null && this.mpInfo.online != null && this.mpInfo.server != null) {
                activity.state += util.format('🌐 %s', this.mpInfo.server.shortname);
                activity.state += util.format(' | %s/%s', this.mpStatsInfo.serverUS, this.mpStatsInfo.serverMAX);
                activity.largeImageText += util.format(' | ID: %s%s', this.mpPrefix, this.mpInfo.playerid)
                if (this.mpInfo.mod == "promods") {
                    activity.state += ' | ProMods';
                }
            } else if (data.telemetry.game.isMultiplayer == true) {
                activity.state = `🌐 TruckersMP`;
            } else {
                activity.state = ('🌐 Singleplayer');
            }


            if (this.locationInfo != null && this.locationInfo.inCity == true) {
                this.inCityDetection = 'In';
            } else if (this.locationInfo != null && this.locationInfo.inCity == false) {
                this.inCityDetection = 'Near';
            } else {
                this.inCityDetection = null;
            }

            if (this.locationInfo && this.inCityDetection && this.locationInfo.location && this.locationInfo.location != null) {
                activity.state += util.format(' | %s %s', this.inCityDetection, this.locationInfo.location);
            }

        }

        return activity;
    }

    isAts(data) {
        return data.telemetry.game.gameID == "ats";
    }

    getDistanceUnit(isAts) {

        if (isAts)
            return config.milesString;

        return config.kmString;
    }

    calculateDistance(value, isAts) {
        if (isAts) {
            return Math.round(value * config.kmToMilesConversion);
        } else {
            return Math.round(value);
        }
    }

    startMPChecker() {
        if (this.mpCheckerInterval == null) {
            var instance = this;
            this.mpCheckerInterval = setInterval(() => {
                instance.checkMpInfo()
            }, this.mpCheckerIntervalTime);
        }
    }
    startLocationChecker() {
        if (this.locationCheckerInterval == null) {
            var instance = this;
            this.locationCheckerInterval = setInterval(() => {
                instance.checkLocationInfo()
            }, this.locationCheckerIntervalTime);
        }
    }

    startMPStatsChecker() {
        if (this.mpStatsCheckerInterval == null) {
            var instance = this;
            this.mpStatsCheckerInterval = setInterval(() => {
                instance.checkMpStatsInfo()
            }, this.mpStatsCheckerIntervalTime);
        }
    }

    startLocationChecker() {
        if (this.locationCheckerInterval == null) {
            var instance = this;
            this.locationCheckerInterval = setInterval(() => {
                instance.checkLocationInfo()
            }, this.locationCheckerIntervalTime);
        }
    }

    resetETCarsData() {
        this.lastData = null;
        this.mpInfo = null;
        this.mpStatsInfo = null;
        this.locationInfo = null;
    }

    resetMPChecker() {
        if (this.mpCheckerInterval != null) {
            clearInterval(this.mpCheckerInterval);
            this.mpCheckerInterval = null;
            this.mpInfo = null;
            this.locationInfo = null;
        }
    }
    resetLocationChecker() {
        if (this.locationCheckerInterval != null) {
            clearInterval(this.locationCheckerInterval);
            this.locationCheckerInterval = null;
            this.locationInfo = null;
        }
    }

    resetMPStatsChecker() {
        if (this.mpStatsCheckerInterval != null) {
            clearInterval(this.mpStatsCheckerInterval);
            this.mpStatsCheckerInterval = null;
            this.mpStatsInfo = null;
        }
    }

    resetLocationChecker() {
        if (this.locationCheckerInterval != null) {
            clearInterval(this.locationCheckerInterval);
            this.locationCheckerInterval = null;
            this.locationInfo = null;
        }
    }

    destroyRPCClient() {
        if (this.rpc != null) {
            var instance = this;
            this.rpc.setActivity({});
            this.rpc.destroy().then(() => {
                instance.rpc = null;
            });
            this.rpcReady = false;
            this.rpcOnChangingState = false;
        }
    }

    checkIfMultiplayer(data) {
        return data.telemetry && data.telemetry.game && data.telemetry.game.isMultiplayer && data.telemetry.user;
    }

    checkMpInfo() {

        var instance = this;

        if (this.lastData != null && this.checkIfMultiplayer(this.lastData)) {


            var url = util.format('https://api.truckyapp.com/v1/richpresence/playerInfo?query=%s', this.lastData.telemetry.user.steamID);

            //console.log(url);
            fetch(url).then((body) => {
                return body.json()
            }).then((json) => {

                if (!json.error) {
                    try {
                        var response = json.response;
                        if (response.onlineState.online) {
                            instance.mpInfo = {
                                online: true,
                                server: response.onlineState.serverDetails,
                                apiserverid: response.onlineState.serverDetails.apiserverid,
                                playerid: response.onlineState.p_id,
                                mod: response.onlineState.serverDetails.mod
                            };
                            instance.locationInfo = {
                                location: response.onlineState.location.poi.realName,
                                inCity: response.onlineState.location.area
                            }
                        } else {
                            instance.mpInfo = {
                                online: false,
                                server: false,
                                apiserverid: false,
                                playerid: false,
                                mod: false
                            }
                            instance.locationInfo = {
                                location: false,
                                inCity: false
                            };
                        };
                    } catch (error) {
                        instance.logger.error(error);
                    }
                } else {
                    instance.mpInfo = null;
                }
            });
        }
    }

    checkMpStatsInfo() {

        var instance = this;

        if (this.lastData != null && this.checkIfMultiplayer(this.lastData) && this.mpInfo.apiserverid != null) {


            var url = util.format('https://api.truckyapp.com/v2/truckersmp/servers');

            //console.log(url);
            fetch(url).then((body) => {
                return body.json()
            }).then((json) => {

                if (!json.error) {
                    try {
                        var server = json.response.servers.find(s => s.id == this.mpInfo.apiserverid);

                        instance.mpStatsInfo = {
                            serverUS: server.players,
                            serverMAX: server.maxplayers,
                            prefix: server.idprefix,
                        };
                    } catch (error) {
                        instance.logger.error(error);
                    }
                } else {
                    instance.mpStatsInfo = null;
                }
            });
        }
    }

    checkLocationInfo() {
        var instance = this;
        if (this.lastData.status == "TELEMETRY") {
            if (this.lastData.telemetry.truck.worldPlacement.x == "0") {
                instance.locationInfo = {
                    location: false,
                    inCity: null,
                };
            } else {

                var url = util.format('https://api.truckyapp.com/v2/map/%s/resolve?x=%s&y=%s', this.lastData.telemetry.game.gameID, this.lastData.telemetry.truck.worldPlacement.x, this.lastData.telemetry.truck.worldPlacement.z);

                //console.log(url);
                fetch(url).then((body) => {
                    return body.json()
                }).then((json) => {

                    if (!json.error) {
                        var response = json.response;
                        instance.locationInfo = {
                            location: response.poi.realName,
                            inCity: response.area,
                        };
                    } else {
                        instance.locationInfo = {
                            location: false,
                            inCity: null,
                        };
                    }
                });
            }
        } else {
            instance.locationInfo = {
                location: false,
                inCity: null,
            };
        }
    }
}

module.exports = RichPresenceManager;