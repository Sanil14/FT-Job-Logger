const { ipcRenderer } = require('electron');

ipcRenderer.on('isOfflineLogin', function(event, text) {
    if (navigator.onLine) {
        event.sender.send("isOfflineReply", false);
    } else {
        event.sender.send("isOfflineReply", true);
    }
})