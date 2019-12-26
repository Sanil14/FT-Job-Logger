const { session } = require("electron").remote;
const version = require("./package.json").version
const isOnline = require("is-online");
const { ipcRenderer } = require('electron');
var isoffline = false,
    serverisoffline = false;

$(document).ready(function() {

    var canvas = $("#alphaText");
    var ctx = canvas[0].getContext("2d");
    ctx.font = "14px Arial";
    ctx.fillStyle = "#ffffff";
    ctx.fillText("Alpha", 7, 18);

    let userdata;
    var ses = session.fromPartition("persist:userinfo")
    ses.cookies.get({}).then(async(cookies) => {
        $(".version").text(`Version ${version}`)
        if (cookies.length < 1) {
            //ipcRenderer.send("logout");
        }
        userdata = JSON.parse(cookies[0].value)
        if (userdata == undefined) {
            ipcRenderer.send("logout");
        } else {
            $(".hello").text(`Hello ${userdata.username}`)
            $(".updatediv").text("No updates to download")
        }

        $(".start").click(function() {
            console.log("Logging should begin")
                //let window = brow.getCurrentWindow();
                //window.loadFile("./logging.html")
            ipcRenderer.send("startlogging", isoffline, serverisoffline);
        })

        $(".logout").click(function() {
            M.toast({
                html: "Successfully Logged Out",
                displayLength: 2000,
                classes: 'yellow darken-4 rounded'
            });
            setTimeout(() => {
                ipcRenderer.send("logout", isoffline);
            }, 1000);
        })

        $(".etcarsdownload").click(function() {
            require("electron").shell.openExternal("https://etcars.menzelstudios.com/downloads/ETCARSx64.exe")
            $(".instructions").show()
            setTimeout(() => {
                $(".instructions").hide();
            }, 20000);
        })
    }).catch((error) => {
        console.log(error);
    })

    function setOffline() {
        $(".offline-ui").show();
        $(".offline-ui-content").show();
        $(".offline-ui-down").show();
    }

    ipcRenderer.on('updateMessages', function(event, text) {
        console.log(text)
        $(".updatediv").text(text);
    })

    ipcRenderer.on("loadHome", async function(event, isOffline, serverIsOffline) {
        if (!isOffline && serverIsOffline) {
            serverisoffline = true;
            setOffline();
            $(".container div:nth-last-child(1)").addClass("serverside");
            $(".serverside").prop("title", "The server is down")
        } else if (isOffline) {
            isoffline = true;
            setOffline();
            $(".container div:last:last-child").addClass("clientside");
            $(".clientside").prop("title", "You have no internet connection")
        }
    })
})