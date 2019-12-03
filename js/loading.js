const { ipcRenderer } = require('electron');
const isOnline = require("is-online");

ipcRenderer.on('isOfflineLogin', async function(event, text) {
    if (await isOnline()) {
        event.sender.send("isOfflineReply", false);
    } else {
        event.sender.send("isOfflineReply", true);
    }
})