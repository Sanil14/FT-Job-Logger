const logger = require("electron-log");
const version = require("./package.json").version
const { session } = require("electron").remote;
const brow = require("electron").remote;
const { ipcRenderer } = require('electron');
const EtCarsClient = require("etcars-node-client");
const axios = require("axios");
const etcars = new EtCarsClient();
var userdata,
    errorcounter = 0;

var ses = session.fromPartition("persist:userinfo")

ses.cookies.get({}).then((cookies) => {
    etcars.connect();
    $(".version").text(`Version ${version}`)

    userdata = JSON.parse(cookies[0].value)
    if (userdata === undefined) ipcRenderer.send("logout");

    log(`Welcome to your logger ${userdata.username}!`, "yellow-text text-accent-2")
    log(`Keep this window open or minimized while trucking to make sure your jobs get logged.`, "yellow-text text-accent-2")
    log(`If you do not keep this window open, we are not responsible for any jobs lost.`, "yellow-text text-accent-2")

    var jobstartedtime;

}).catch((error) => {
    console.log("Error retrieving cookies: " + error);
    log(JSON.stringify(error));
})

etcars.on('data', function(data) {
    try {
        var info = [];
        if (data.status == "JOB STARTED") {
            log("JOB STARTED");
            jobstartedtime = Date.now();
        }
        if (data.status == "JOB FINISHED") {
            if (typeof data.telemetry != 'undefined' && data.telemetry) {
                if (typeof data.jobData != 'undefined' && data.jobData) {
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
                    let fee = data.telemetry.job.isLate ? true : false
                    info.push(fee)
                    info.push(jobstartedtime)
                    info.push(Date.now())
                    info.push(data.jobData.topSpeed)
                    info.push(data.jobData.speedingCount)
                    info.push(data.jobData.collisionCount)
                    info.push(calcDamage(data))
                    info.push(data.jobData.truckMake)
                    info.push(data.jobData.truckModel)
                    log(JSON.stringify(info));
                    if (info.length > 1) {
                        log("JOB SUBMITTED")
                        log(`https://falconites.com/dashboard/api/v1/jobs?key=9xsyr1pr1miyp45&data=${encodeURIComponent(info.join(","))}`);
                        axios.get(`https://falconites.com/dashboard/api/v1/jobs?key=9xsyr1pr1miyp45&data=${encodeURIComponent(info.join(","))}`).then(function(response) {
                            log(JSON.stringify(response))
                        }).catch(function(err) {
                            log(JSON.stringify(err));
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