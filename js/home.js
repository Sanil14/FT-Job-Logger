const { session } = require("electron").remote;
const version = require("./package.json").version
const { ipcRenderer } = require('electron');
var isoffline = false;

$(document).ready(function() {

    let userdata;
    var ses = session.fromPartition("persist:userinfo")
    ses.cookies.get({}).then((cookies) => {
        $(".version").text(`Version ${version}`)
        if (cookies.length < 1) {
            //ipcRenderer.send("logout");
        }
        if (!navigator.onLine) {
            setOffline();
            isoffline = true;
            $(".container div:nth-last-child(1)").addClass("clientside");
            $(".clientside").prop("title", "You have no internet connection")
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
            ipcRenderer.send("startlogging");
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

    ipcRenderer.on("loadHome", function(event, isOffline) {
        if (isOffline && navigator.onLine) {
            setOffline();
            isoffline = true;
            $(".container div:nth-last-child(1)").addClass("serverside");
            $(".serverside").prop("title", "The server is down")
        }
    })
})