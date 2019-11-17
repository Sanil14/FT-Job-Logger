const electron = require("electron");
const path = require("path");
const { session } = require("electron");
const { ipcMain } = require("electron");
const logger = require("electron-log");
const notifier = require("node-notifier");
const { autoUpdater } = require("electron-updater");
const { app, Menu, Tray, Notification } = require('electron');
const axios = require("axios");
autoUpdater.logger = logger;
autoUpdater.logger.transports.file.level = "info"
autoUpdater.autoDownload = true;
var windowHidden,
    win = null,
    isQuitting,
    isoffline;

const gotTheLock = app.requestSingleInstanceLock()

if (!gotTheLock) {
    app.quit()
} else {
    app.on('ready', createWindow);
}

function createWindow() {
    logger.info("Starting up Electron...")
    win = new electron.BrowserWindow({
        webPreferences: {
            nodeIntegration: true,
        },
        width: 850,
        height: 500,
        icon: "./assets/falcon_logo.jpg",
        autoHideMenuBar: true
            //devTools: false,
            //frame: false
    })
    windowHidden = false;

    win.loadFile("login.html")
    sendStatusToWindow("No updates available");
    let ses = session.fromPartition("persist:userinfo")

    ses.cookies.get({}).then(async(cookies) => {
        if (cookies.length < 1) return;
        var parsed = JSON.parse(cookies[0].value);
        if (parsed.verification && parsed.key != undefined) {
            console.log("SESSION DETECTED")
            win.webContents.once('dom-ready', () => {
                win.webContents.send("isOfflineLogin")
                ipcMain.once("isOfflineReply", (event, offline) => {
                    isoffline = offline;
                    console.log(isoffline);
                    if (isoffline) {
                        win.loadFile("logging.html").then(() => {
                            updateTrackingMenu(true, isoffline);
                        });
                    } else {
                        axios.get('https://falconites.com/dashboard/api/v1/users?key=9xsyr1pr1miyp45&login=' + parsed.key).then(function(response) {
                            var data = response.data;
                            if (data.status != "202") {
                                ses.cookies.remove("https://dashboard.falconites.com", "userinfo").then(() => {
                                    win.loadFile("login.html");
                                }).catch((error) => {
                                    if (error) console.error(error);
                                })
                            } else {
                                win.loadFile("logging.html").then(() => {
                                    updateTrackingMenu(true, false);
                                })
                            }
                        }).catch(function(err) {
                            console.log(err);
                            if (err.errno == "ENOTFOUND" || err.code == "ENOTFOUND" || err.code == "ECONNREFUSED" || err.errno == "ECONNREFUSED") {
                                win.loadFile("logging.html").then(() => {
                                    isoffline = true;
                                    updateTrackingMenu(true, isoffline);
                                });
                            }
                        })
                    }
                })
            })
        } else {
            win.loadFile("login.html").then(() => {
                updateTrackingMenu(true, false);
            })
        }
    }).catch((error) => {
        console.log(error);
    })

    console.log("Booting up logger");
    iconPath = path.join(__dirname, "/assets/falcon_icon.png");
    let trayIcon = electron.nativeImage.createFromPath(iconPath);
    tray = new Tray(trayIcon);
    tray.setToolTip("FT Job Logger")
    var context = [{
            label: 'Show/Hide App',
            id: "showapp",
            click: function() {
                if (windowHidden) {
                    win.show();
                } else {
                    windowHidden = true;
                    win.hide();
                }
            }
        },
        {
            label: 'Start Logging',
            id: 'start',
            click: function() {
                updateTrackingMenu(false, isoffline);
            }
        },
        {
            label: 'Stop Logging',
            id: 'stop',
            enabled: false,
            click: function() {
                updateTrackingMenu(false, isoffline);
            }
        },
        {
            type: "separator"
        },
        {
            label: "Check for Updates",
            click: function() {
                autoUpdater.checkForUpdates();
            }
        },
        {
            label: 'Exit',
            click: function() {
                isQuitting = true;
                app.quit();
            }
        }
    ]
    var contextMenu = Menu.buildFromTemplate(context);
    initTray(tray);

    function updateTrackingMenu(onStartup, isOffline) {
        let url = win.webContents.getURL(),
            page = url.split('/').pop();
        if (page == "logging.html" && !onStartup) { // Stop tracking
            contextMenu.getMenuItemById("start").enabled = true;
            contextMenu.getMenuItemById("stop").enabled = false;
            initTray(tray);
            win.loadFile("home.html").then(() => {
                win.webContents.send("loadHome", isOffline);
            })
        } else if (page == "home.html" || onStartup) { // Start Tracking
            contextMenu.getMenuItemById("start").enabled = false;
            contextMenu.getMenuItemById("stop").enabled = true;
            initTray(tray);
            if (!onStartup) {
                win.loadFile("logging.html").then(() => {
                    win.webContents.send("loadLogging", isOffline);
                })
            } else {
                win.webContents.send("loadLogging", isOffline);
            }
        } else if (page == "login.html") { // Not logged in
            contextMenu.getMenuItemById("start").enabled = false;
            contextMenu.getMenuItemById("stop").enabled = false;
            initTray(tray);
        }
    }

    win.on('minimize', function(event) {
        windowHidden = true;
        //sendNotif();
        event.preventDefault();
        win.hide();
    });

    win.on('close', function(event) {
        //sendNotif();
        if (!isQuitting) {
            windowHidden = true;
            event.preventDefault();
            win.hide();
        }
        return false;
        //event.returnValue = false;
    });

    win.on('show', function(e) {
        windowHidden = false;
    })

    function initTray(tray) {
        tray.setContextMenu(contextMenu);
    }

    tray.on("click", function() {
        win.show();
    })

    win.on('closed', () => {
        win = null;
    })

    app.on('second-instance', (event, commandLine, workingDirectory) => {
        // Someone tried to run a second instance, we should focus our window.
        if (win) {
            if (windowHidden) {
                win.show()
                win.focus()
            }
        }
    })

    ipcMain.on('login-success', () => {
        updateTrackingMenu(false, isoffline);
        win.loadFile("home.html");
    })

    ipcMain.on('logout', (event, isOffline) => {
        //updateTrackingMenu(false);
        ses.cookies.remove("https://dashboard.falconites.com", "userinfo").then(() => {
            win.loadFile("login.html");
        }).catch((error) => {
            if (error) console.error(error);
        })
    })

    ipcMain.on('startlogging', () => {
        updateTrackingMenu(false, isoffline);
    })

    ipcMain.on('stoplogging', (event, isOffline) => {
        updateTrackingMenu(false, isOffline);
    })

    ipcMain.on('unexpectederror', () => {
        console.log("Restarting application...")
        isQuitting = true;
        app.relaunch();
        app.quit();
    })
}

/* AUTO UPDATER CODE FROM HERE */

function sendStatusToWindow(msg) {
    win.webContents.send("updateMessages", msg)
}

autoUpdater.on('checking-for-update', () => {
    console.log("Checking for updates...")
    sendStatusToWindow('Checking for update...');
})
autoUpdater.on('update-available', (info) => {
    notifier.notify({
        title: "FT Job Logger",
        message: `A new version ${info.version} is available, it will begin downloading shortly`,
        icon: path.join(__dirname, '/assets/falcon_logo.jpg'),
        sound: true
    }, function(err, resp) {
        logger.info("Download Available")
    })
    sendStatusToWindow('Update available.');
})
autoUpdater.on('update-not-available', (info) => {
    sendStatusToWindow('No new update');
})
autoUpdater.on('error', (err) => {
    sendStatusToWindow('Error in update. Contact Dev.');
})
autoUpdater.on('download-progress', (progressObj) => {
    let log_message = 'Downloaded ' + Math.round(progressObj.percent) + '%';
    sendStatusToWindow(log_message);
})
autoUpdater.on('update-downloaded', (info) => {
    //app.removeAllListeners("window-all-closed")
    sendStatusToWindow('Update downloaded. Restart to install.');
    //autoUpdater.quitAndInstall();
});

app.on('ready', function() {
    autoUpdater.checkForUpdates();
})

app.on('window-all-closed', () => {
    app.quit();
});

process.on("uncaughtException", (err) => {
    logger.error(err);
})