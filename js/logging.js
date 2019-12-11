const logger = require("electron-log");
const version = require("./package.json").version
const pass = require("./package.json").config.pass
const { session } = require("electron").remote;
const remote = require("electron").remote;
const { ipcRenderer } = require('electron');
const EtCarsClient = require("./js/etcars");
const isOnline = require("is-online");
const axios = require("axios");
const etcars = new EtCarsClient();
const CryptoJS = require("crypto-js");
const fs = require('fs');
const RichPresenceManager = require('./js/rpcmanager.js');
var presenceManager = new RichPresenceManager();

etcars.enableDebug = true;
var userdata,
    errorcounter = 0,
    unexerrorc = 0,
    isoffline = false,
    serverisoffline = false,
    comparejob;

$(document).ready(function() {


    var ses = session.fromPartition("persist:userinfo")

    ses.cookies.get({}).then(async(cookies) => {
        etcars.connect();
        $(".version").text(`Version ${version}`)
        if (isoffline) {
            isoffline = true;
            serverisoffline = true;
            setOffline();
            $(".container div:last:last-child").addClass("clientside");
            $(".clientside").prop("title", "You have no internet connection")
        }

        userdata = JSON.parse(cookies[0].value)
        if (userdata === undefined) ipcRenderer.send("logout");

        log(`FT Job Logger Version: ${version}`, "yellow-text text-accent-2")
        log(`Welcome to your logger ${userdata.username}! Keep this window open/minimized to tray`, "yellow-text text-accent-2")
        log(`If you close this window, you will lose connection to game and server and your jobs will not be logged.`, "yellow-text text-accent-2")

    }).catch((error) => {
        console.log("Error retrieving cookies: " + error);
        log(JSON.stringify(error));
    })

    etcars.on('data', async function(data) {
        try {
            if (navigator.onLine) {
                presenceManager.onData(data);
            }
            if (data.status == "JOB STARTED") {
                log("Job start detected");
                if (!(await isOnline())) {
                    log(`Internet Connection: <span class="red-text">Disconnected</span>`, "yellow-text text-accent-2")
                    isoffline = true;
                    serverisoffline = true;
                    setOffline();
                    $(".container div:last:last-child").addClass("clientside");
                    $(".clientside").prop("title", "You have no internet connection")
                }
            }
            if (data.status == "JOB FINISHED") {
                if (typeof data.telemetry != 'undefined' && data.telemetry) {
                    if (typeof data.jobData != 'undefined' && data.jobData) {
                        var info = [];
                        /*info.push(2)
                        if(data.jobData.status == 3) { // FOR OUTLINING IF JOB WAS FINISHED OR CANCELLED
                            info[0] = 3;
                        }
                        */
                        info.push(data.jobData.isMultiplayer)
                        info.push(userdata.userid)
                        info.push(data.jobData.gameID)
                        info.push(data.jobData.sourceCity)
                        info.push(data.jobData.sourceCompany == "" ? "Special Transport Job" : data.jobData.sourceCompany)
                        info.push(data.jobData.destinationCity)
                        info.push(data.jobData.destinationCompany == "" ? "Special Transport Job" : data.jobData.destinationCompany)
                        info.push(Math.round(data.jobData.distanceDriven))
                        info.push(data.jobData.fuelBurned)
                        info.push(data.jobData.income)
                        info.push(data.telemetry.job.cargo)
                        info.push(data.telemetry.job.mass)
                        info.push(data.jobData.late)
                        info.push(data.jobData.realTimeStarted)
                        info.push(data.jobData.realTimeEnded)
                        info.push(data.jobData.topSpeed)
                        info.push(data.jobData.speedingCount)
                        info.push(data.jobData.collisionCount)
                        info.push(calcDamage(data))
                        info.push(data.jobData.truckMake)
                        info.push(data.jobData.truckModel)
                        newjob = CryptoJS.SHA256(JSON.stringify(info));
                        console.log(comparejob);
                        if (JSON.stringify(comparejob) == JSON.stringify(newjob)) {
                            console.log("multiple same jobs being posted... preventing that.");
                            return;
                        }
                        comparejob = CryptoJS.SHA256(JSON.stringify(info));
                        console.log(comparejob) // REMOVE BEFORE RELEASE
                        log("<b>Outputting Job Values only for Alpha Testing</b>:<br>" + JSON.stringify(info)); // REMOVE BEFORE RELEASE
                        if (info.length > 1 && await isOnline()) {
                            log("Attempting to submit job...")
                            axios.post(`https://falconites.com/dashboard/api/v1/jobs?key=9xsyr1pr1miyp45&data=${encodeURIComponent(info.join(","))}`)
                                .then(function(response) {
                                    var data = response.data;
                                    if (data.status != "202") {
                                        if (data.status == "400") {
                                            log("Error 400 submitting job: Contact Dev")
                                            logger.info(data.error)
                                        } else {
                                            log("Error submitting job: " + data.error, "red-text");
                                        }
                                    } else {
                                        log("Job has been successfully submitted!", "green-text")
                                    }
                                }).catch(function(err) {
                                    console.log("Could not connect to server:" + err.errno);
                                    logger.error("Could not connect to server:" + err.errno);
                                    if (err.errno == "ENOTFOUND" || err.code == "ENOTFOUND" || err.code == "ECONNREFUSED" || err.errno == "ECONNREFUSED" || err.errno == "EAI_AGAIN") {
                                        log("Unable to submit job. Server might be offline.", "red-text");
                                        log("All jobs will be locally stored.", "orange-text text-lighten-2")
                                        serverisoffline = true;
                                        setOffline();
                                        $(".container div:last:last-child").addClass("clientside");
                                        $(".clientside").prop("title", "You have no internet connection")
                                        log("Attempting to locally store job...")
                                        setOfflineJobs(info);
                                    } else {
                                        log("Unexpected error when submitting job: Contact Dev", "red-text")
                                        logger.info("Error:" + JSON.stringify(err));
                                    }
                                })
                        } else {
                            if (!isoffline) {
                                isoffline = true;
                                serverisoffline = true;
                                setOffline();
                                $(".container div:last:last-child").addClass("clientside");
                                $(".clientside").prop("title", "You have no internet connection")
                            }
                            log("Attempting to locally store job...")
                            setOfflineJobs(info);
                        }
                    }
                }
            }
        } catch (err) {
            log("Encountered an error: Contact Dev.")
            logger.error(JSON.stringify(err));
        }
    })

    $(".stop button").click(function() {
        log("Beware, Any logging after this will not be recorded!", "red-text")
        setTimeout(async() => {
            if (await isOnline()) {
                isoffline = false;
                //serverisoffline = false;
            } else {
                isoffline = true;
                serverisoffline = true;
            }
            log(`Stopped the Logger`, "orange-text text-lighten-2");
            presenceManager.disableRPC();
            ipcRenderer.send("stoplogging", isoffline, serverisoffline);
        }, 2000);
    })

    $(".uploadbutton").click(async function() {
        await getOfflineJobs();
    })

    etcars.on('connect', function(data) {
        log("Connected to ETCars. Ready to log jobs!", "green-text")
        errorcounter = 0;
    })

    etcars.on('error', function(data) {
        if (errorcounter >= 1) {
            return;
        }
        errormsg = data.errorMessage;
        if (errormsg == "ETCars is not running") {
            errormsg = "Game is not running or ETCars is not installed!"
            presenceManager.disableRPC();
        }
        log(`${errormsg}`, "red-text");
        errorcounter += 1
    })

    etcars.on('unexpectedError', function(data) {
        if (unexerrorc >= 1) {
            return;
        }
        presenceManager.disableRPC();
        ipcRenderer.send("unexpectederror");
        errorm = "Unexpected error with logger. Refreshing...";
        logger.error(data.errorMessage)
        log(`${errorm}`, "red-text")
        unexerrorc += 1;
    })

    function calcDamage(data) {
        let totalDamageFinish = data.jobData.finishTrailerDamage;
        let totalDamageStart = data.jobData.startTrailerDamage;
        return totalDamageFinish - totalDamageStart;
    }

    function setOffline() {
        $(".offline-ui").show();
        $(".offline-ui-content").show();
        $(".offline-ui-down").show();
    }

    function hideOffline() {
        $(".offline-ui").fadeOut(1000);
        $(".offline-ui-content").fadeOut(1000);
        $(".offline-ui-down").fadeOut(1000);
    }

    function log(msg, color) {
        var d = new Date();
        var time = `${d.getDate()}/${d.getMonth()}/${d.getFullYear()} ${(d.getHours() < 10 ? '0' : '') + d.getHours()}:${(d.getMinutes() < 10 ? '0' : '') + d.getMinutes()}:${(d.getSeconds() < 10 ? '0' : '') + d.getSeconds()}`;
        $(".consolediv").append(`<p class="left-align ${color ? color : ""}">[${time}] ${msg}</p>`)
        var d = $('.consolediv');
        d.scrollTop(d.prop("scrollHeight"));
        logger.info(msg);
    }

    ipcRenderer.on('updateMessages', function(event, text) {
        console.log(text)
        $(".updatediv").text(text);
    })

    ipcRenderer.on("loadLogging", async function(event, isOffline, serverIsOffline) {
        if (!isOffline && serverIsOffline) {
            serverisoffline = true;
            setOffline();
            $(".container div:nth-last-child(1)").addClass("serverside");
            $(".serverside").prop("title", "The server is down")
        } else if (isOffline) {
            isoffline = true;
            serverisoffline = true;
            setOffline();
            $(".container div:last:last-child").addClass("clientside");
            $(".clientside").prop("title", "You have no internet connection")
        }
        log(`Internet Connection: <span class="${!isoffline ? "green-text" : "red-text"}">${!isoffline ? "Connected" : "Disconnected"}</span>`, "yellow-text text-accent-2")
        log(`Server Connection: <span class="${!serverisoffline ? "green-text" : "red-text"}">${!serverisoffline ? "Connected" : "Disconnected"}</span>`, "yellow-text text-accent-2")
    })

    async function getOfflineJobs() {
        path = remote.app.getPath("userData") + "\\Local Jobs";
        try {
            if (!(await isOnline())) {
                isoffline = true;
                serverisoffline = true;
                setOffline();
                $(".container div:last:last-child").addClass("clientside");
                $(".clientside").prop("title", "You have no internet connection")
                return log("You do not have an active internet connection!", "red-text")
            } else if (!serverisoffline) {
                hideOffline();
                isoffline = false;
            }
            if (!fs.existsSync(path) || !fs.existsSync(path + "\\localjobs.dat")) {
                return log("There are no offline jobs to upload to the server!")
            }
            let fpath = path + "\\localjobs.dat";
            fs.readFile(fpath, function(err, data) {
                if (err) {
                    logger.error(err);
                    console.log(err);
                }
                if (typeof(data.toString()) == "undefined" || data.toString() == null || !data.toString()) {
                    return log("There are no offline jobs to upload to the server!")
                } else {
                    axios.get("https://falconites.com/dashboard")
                        .then(function(resp) {
                            hideOffline();
                            var bytes = CryptoJS.AES.decrypt(data.toString(), pass);
                            var res = JSON.parse(bytes.toString(CryptoJS.enc.Utf8));
                            let keys = Object.keys(res);
                            console.log(res);
                            let len = keys.length,
                                x = 0;
                            for (const job of keys) {
                                log(`Attempting to submit job #${x += 1}`)
                                axios.post(`https://falconites.com/dashboard/api/v1/jobs?key=9xsyr1pr1miyp45&data=${encodeURIComponent(res[job].join(","))}`)
                                    .then(function(response) {
                                        var data = response.data;
                                        if (data.status != "202") {
                                            if (data.status == "400") {
                                                log("Error 400 submitting job: Contact Dev")
                                                logger.info(data.error)
                                            } else {
                                                log("Error submitting job: " + data.error, "red-text");
                                            }
                                        } else {
                                            log("Job has been successfully submitted!", "green-text")
                                            fs.unlink(fpath, function(err) {
                                                if (err) {
                                                    logger.error(err);
                                                    console.log(err);
                                                }
                                            })
                                        }
                                    }).catch(function(err) {
                                        if (err.errno == "ENOTFOUND" || err.code == "ENOTFOUND" || err.code == "ECONNREFUSED" || err.errno == "ECONNREFUSED" || err.errno == "EAI_AGAIN") {
                                            serverisoffline = true;
                                            log("Unable to submit job. Server might still be offline. Try again later.", "red-text");
                                        } else {
                                            log("Unexpected error when submitting job: Contact Dev", "red-text")
                                            logger.info("Error:" + JSON.stringify(err));
                                        }
                                    })
                            }
                        }).catch(function(err) {
                            if (err.errno == "ENOTFOUND" || err.code == "ENOTFOUND" || err.code == "ECONNREFUSED" || err.errno == "ECONNREFUSED" || err.response.status == 404) {
                                serverisoffline = true;
                                return log("The server is down. Try again later.", "red-text")
                            }
                            logger.error(err);
                            console.log(err);
                        })
                }
            })
        } catch (err) {
            log("Error fetching local jobs. Contact Dev.", "red-text")
            logger.error(err);
            console.log(err);
        }
    }


    function setOfflineJobs(jobdata) {

        path = remote.app.getPath("userData") + "\\Local Jobs";
        res = {};
        try {
            if (!fs.existsSync(path)) {
                fs.mkdirSync(path);
                fs.writeFile(`${path}\\localjobs.dat`, "", function(err) {
                    if (err) {
                        logger.error(err);
                        console.log(err);
                    }
                })
            } else if (!fs.existsSync(path + "\\localjobs.dat")) {
                fs.writeFile(`${path}\\localjobs.dat`, "", function(err) {
                    if (err) {
                        logger.error(err);
                        console.log(err);
                    }
                })
            }
            let fpath = path + "\\localjobs.dat";
            fs.readFile(fpath, function(err, data) {
                if (typeof(data.toString()) == "undefined" || data.toString() == null || !data.toString()) {
                    var res = {
                        1: jobdata
                    };
                } else {
                    var bytes = CryptoJS.AES.decrypt(data.toString(), pass);
                    var res = JSON.parse(bytes.toString(CryptoJS.enc.Utf8));
                    len = Object.keys(res).length;
                    res[len + 1] = jobdata;
                }
                var encrypted = CryptoJS.AES.encrypt(JSON.stringify(res), pass);
                fs.writeFile(fpath, encrypted, function(err) {
                    if (err) {
                        logger.error(err);
                        console.log(err);
                    }
                    log("Job successfully saved locally. To upload, click the upload button when connection is regained", "green-text")
                })
            })
        } catch (err) {
            logger.error(err);
            console.log(err);
        }
    } // ["6","ets2","Bratislava","test","Graz","test",2.34434,234.4,1234,"SOMETHING",70000,false,3048305705,8407505274,18.545,0,15,0.578037,"Scania","S"]

});