const { session } = require("electron").remote;
const { BrowserWindow } = require("electron").remote;
const brow = require("electron").remote;
const version = require("./package.json").version
const { ipcRenderer } = require('electron');

let userdata;
var ses = session.fromPartition("persist:userinfo")
ses.cookies.get({}).then((cookies) => {
    $(".version").text(`Version ${version}`)
    console.log(cookies)
    if (cookies.length < 1) {
        //ipcRenderer.send("logout");
    }
    userdata = JSON.parse(cookies[0].value)
    if (userdata == undefined) {
        ipcRenderer.send("logout");
    } else {
        $(".hello").text(`Hello ${userdata.username}`)
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
            ipcRenderer.send("logout");
        }, 1000);
    })
}).catch((error) => {
    console.log(error);
})