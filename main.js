const electron = require("electron");
const path = require("path");
const { session } = require("electron");
const { ipcMain } = require("electron");
const logger = require("electron-log");
const { autoUpdater } = require("electron-updater");
const { app, Menu, Tray, Notification } = require('electron');
autoUpdater.logger = logger;
autoUpdater.logger.transports.file.level = "info"
autoUpdater.autoDownload = true;
var windowHidden,
    win = null,
    page,
    isQuitting;

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
            //resizable: false,
            //frame: false
    })
    let url = win.getURL(),
        page = url.split('/').pop();
    windowHidden = false;

    function sendNotif() {
        let myNotification = new Notification({
            title: "FT Job Logger",
            subtitle: "Application is now hidden in tray",
            body: "For your convenience, the FT Job Logger application is now hidden in tray.",
            icon: "./assets/falcon_logo.jpg",
            silent: true
        }).show();
    }

    win.loadFile("login.html")
    sendStatusToWindow("No updates available");
    let ses = session.fromPartition("persist:userinfo")

    ses.cookies.get({}).then((cookies) => {
        if (cookies.length < 1) return;
        parsed = JSON.parse(cookies[0].value);
        if (parsed.verification && parsed.logged_in) {
            console.log("SESSION DETECTED") // Add check to see if cookies are still updated
            win.loadFile("home.html").then(() => {
                updateTrackingMenu();
            })
        } else {
            win.loadFile("login.html").then(() => {
                updateTrackingMenu();
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
                updateTrackingMenu();
            }
        },
        {
            label: 'Stop Logging',
            id: 'stop',
            enabled: false,
            click: function() {
                updateTrackingMenu();
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

    function updateTrackingMenu() {
        let url = win.webContents.getURL(),
            page = url.split('/').pop();
        if (page == "logging.html") { // Stop tracking
            contextMenu.getMenuItemById("start").enabled = true;
            contextMenu.getMenuItemById("stop").enabled = false;
            initTray(tray);
            win.loadFile("home.html")
        } else if (page == "home.html") { // Start Tracking
            contextMenu.getMenuItemById("start").enabled = false;
            contextMenu.getMenuItemById("stop").enabled = true;
            initTray(tray);
            win.loadFile("logging.html")
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
        updateTrackingMenu();
        win.loadFile("home.html");
    })

    ipcMain.on('logout', () => {
        //updateTrackingMenu();
        ses.cookies.remove("https://dashboard.falconites.com", "userinfo").then(() => {
            win.loadFile("login.html");
        }).catch((error) => {
            if (error) console.error(error);
        })
    })

    ipcMain.on('startlogging', () => {
        updateTrackingMenu();
        win.loadFile("logging.html")
    })

    ipcMain.on('stoplogging', () => {
        updateTrackingMenu();
        win.loadFile("home.html")
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
    if (page != "home.html") {
        if (page != "login.html") {
            win.loadFile("home.html");
        }
    }
    let myNotification = new Notification({
        title: "FT Job Logger",
        subtitle: "New Update available",
        body: `A new version ${info.version} is available, it will begin downloading shortly`,
        icon: "./assets/falcon_logo.jpg",
        silent: false
    }).show();
    sendStatusToWindow('Update available.');
})
autoUpdater.on('update-not-available', (info) => {
    sendStatusToWindow('Update not available.');
})
autoUpdater.on('error', (err) => {
    sendStatusToWindow('Error in auto-updater. ' + err);
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