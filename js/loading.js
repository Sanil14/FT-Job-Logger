const { ipcRenderer } = require('electron');
const isOnline = require("is-online");

var canvas = $("#alphaText");
var ctx = canvas[0].getContext("2d");
ctx.font = "14px Arial";
ctx.fillStyle = "#ffffff";
ctx.fillText("Alpha", 7, 18);

ipcRenderer.on('isOfflineLogin', async function(event, text) {
    if (await isOnline()) {
        event.sender.send("isOfflineReply", false);
    } else {
        event.sender.send("isOfflineReply", true);
    }
})