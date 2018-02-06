const { remote } = require('electron');
const path = require('path');
const url = require('url');
const WebCamera = require("webcamjs");

WebCamera.set({
  width: 400,
  height: 320,
  image_format: 'jpeg',
  jpeg_quality: 90,
  force_flash: false,
  fps: 45
});
let enabled = false;
// Use require to add webcamjs

const logIn = function() {
  remote
    .getCurrentWindow()
    .loadURL(url.format({
      pathname: path.join(__dirname, "dashboard.pug"),
      protocol: 'file:',
      slashes: true
    }));
}

let snapImage = function() {
  WebCamera.snap(function(data_uri) {
    document.getElementById('webcam-capture-modal')
      .innerHTML = '<img src="'+data_uri+'"/>';
  });
}

document
  .querySelector("#login-button")
  .onclick = function() {
    console.log("INside quere")
    logIn();
  }

document
  .querySelector("#login-form")
  .onsubmit = function(e) {
    e.preventDefault();
    logIn();
}

document
  .querySelector("#image-login")
  .onclick = function(e) {
    // Click the image of user
    document
      .querySelector("#user-image-modal")
      .style.display = "flex";
    if(!enabled) {
      enabled = true;
      document.querySelector("#webcam-capture-modal")
        .innerHTML = "";
      WebCamera.attach('#webcam-capture-modal');
     console.log("The camera has been started");
    } else {
      enabled = false;
      WebCamera.reset();
      console.log("The camera has been disabled");
    }
  }


document
  .querySelector("#user-image-modal")
  .onclick = function(e) {
    e.stopPropagation();
    enabled = false;
    WebCamera.reset();
    document
      .querySelector("#user-image-modal")
      .style.display = "none";
  }

document
  .querySelector(".image-button-container")
  .onclick = function(e ){
    e.stopPropagation();
  }

let captureImageAndPaste = function(e) {
   /* document.querySelector("#capture-button")
      .outerHTML = '<button class="mui-btn\
        mui-btn--danger mui-btn--large"\
        id="capture-button" onClick="captureImageAndPaste;">\
          Capture Again\
        </button>'; */
    e.target.style.display = "none";
    document.querySelector("#image-upload-button")
      .style.display = "flex";
    snapImage();
  }

document
  .querySelector("#capture-button")
  .onclick = captureImageAndPaste;
