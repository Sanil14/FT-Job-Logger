const logger = require("electron-log");
const version = require("./package.json").version
const { session } = require("electron").remote;
const brow = require("electron").remote;
const { ipcRenderer } = require('electron');
const EtCarsClient = require("./js/etcars");
const axios = require("axios");
const etcars = new EtCarsClient();
etcars.enableDebug = true;
var userdata,
    errorcounter = 0,
    unexerrorc = 0;

var ses = session.fromPartition("persist:userinfo")

ses.cookies.get({}).then((cookies) => {
    etcars.connect();
    $(".version").text(`Version ${version}`)

    userdata = JSON.parse(cookies[0].value)
    if (userdata === undefined) ipcRenderer.send("logout");

    log(`Welcome to your logger ${userdata.username}! Keep this window open/minimized to tray`, "yellow-text text-accent-2")
    log(`If you close this window, you will lose connection with game and server and your jobs will not be logged.`, "yellow-text text-accent-2")

}).catch((error) => {
    console.log("Error retrieving cookies: " + error);
    log(JSON.stringify(error));
})

etcars.on('data', function(data) {
    try {
        if (data.status == "JOB STARTED") {
            log("Job start detected");
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
                    info.push(userdata.userid)
                    info.push(data.jobData.gameID)
                    info.push(data.jobData.sourceCity)
                    info.push(data.jobData.sourceCompany)
                    info.push(data.jobData.destinationCity)
                    info.push(data.jobData.destinationCompany)
                    info.push(data.jobData.distanceDriven)
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
                    console.log(info) // REMOVE BEFORE RELEASE
                    log("<b>Outputting Job Values only for Alpha Testing</b>:<br>" + JSON.stringify(info)); // REMOVE BEFORE RELEASE
                    if (info.length > 1) {
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
                                log("Unexpected error when submitting job: Contact Dev")
                                logger.info("Error:" + JSON.stringify(err));
                            })
                    }
                }
            }
        }
    } catch (err) {
        log(JSON.stringify(err));
    }
})

$(".stop button").click(function() {
    log("Beware, Any logging after this will not be recorded!", "red-text")
    setTimeout(() => {
        log(`Stopped the Logger`, "orange-text text-lighten-2");
        setTimeout(() => {
            //let window = brow.getCurrentWindow();
            //window.loadFile("./home.html");
            ipcRenderer.send("stoplogging");
        }, 2000);
    }, 2000);
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
    }
    log(`${errormsg}`, "red-text");
    errorcounter += 1
})

etcars.on('unexpectedError', function(data) {
    if (unexerrorc >= 1) {
        return;
    }
    ipcRenderer.send("unexpectederror");
    errorm = "Unexpected error with logger. Restart immediately";
    logger.error(data.errorMessage)
    log(`${errorm}`, "red-text")
    unexerrorc += 1;
})

function calcDamage(data) {
    let totalDamageFinish = data.jobData.finishTrailerDamage;
    let totalDamageStart = data.jobData.startTrailerDamage;
    return totalDamageFinish - totalDamageStart;
}

function log(msg, color) {
    var d = new Date();
    var time = `${d.getDate()}/${d.getMonth()}/${d.getFullYear()} ${d.getHours()}:${d.getMinutes()}:${d.getSeconds()}`;
    $(".consolediv").append(`<p class="left-align ${color ? color : ""}">[${time}] ${msg}</p>`)
    logger.info(msg);
}

ipcRenderer.on('updateMessages', function(event, text) {
    console.log(text)
    $(".updatediv").text(text);
})