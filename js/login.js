const axios = require("axios");
const { ipcRenderer } = require('electron');
const { remote } = require('electron')
const version = require("./package.json").version

$(".version").text(`Version ${version}`)

var button = document.getElementById("submitButton");
button.addEventListener("click", async function() {
    var key = document.getElementById("key");
    if (!key || key.value === "" || key.value.split(" ").length > 1) {
        return M.toast({
            html: "Please enter a key!",
            displayLength: 4000,
            classes: 'yellow darken-4 rounded'
        });
    }
    axios.get('https://falconites.com/dashboard/api/v1/users?key=9xsyr1pr1miyp45&login=' + key.value)
        .then(function(response) {
            var data = response.data;
            if (data.status != "202") {
                return M.toast({
                    html: "Invalid Key!",
                    displayLength: 4000,
                    classes: 'red rounded'
                });
            } else {
                M.toast({
                    html: "Successful!",
                    displayLength: 2000,
                    classes: 'green rounded'
                });
                var info = {
                    verification: true,
                    userid: data.userid,
                    username: `${data.username}`,
                    key: key.value
                };
                var ses = remote.session.fromPartition("persist:userinfo");
                //console.log(ses);
                ses.cookies.set({
                    url: 'https://dashboard.falconites.com',
                    name: 'userinfo',
                    value: JSON.stringify(info),
                    expirationDate: 9999999999
                }).then(() => {
                    ses.cookies.get({}).then((cookies) => { // DELETE FOR PROD VERSION
                        console.log(cookies);
                    }).catch((error) => {
                        console.log(error);
                    })
                    if (key.value != "") {
                        setTimeout(() => {
                            ipcRenderer.send("login-success")
                        }, 1000);
                    }
                }, (error) => {
                    console.error(error);
                    return M.toast({
                        html: "Error in storing data!",
                        displayLength: 4000,
                        classes: 'red rounded'
                    });
                });
            }
        })
        .catch(function(error) {
            console.log(JSON.stringify(error));
            M.toast({
                html: "404 Server Error",
                displayLength: 4000,
                classes: 'red darken-4 rounded'
            });
        })
})

$(document).ready(function() {
    console.log("I am loaded.");
    ipcRenderer.on('updateMessages', function(event, text) {
        console.log(text)
        $(".updatediv").text(text);
    })
});