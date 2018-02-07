const { remote } = require('electron');
const path = require('path');
const url = require('url');
const WebCamera = require("webcamjs");
const CONFIG = require("./config/config.js");


/* GLOBAL Configurations */
const apiRequestParams = {
  "returnFaceId": "true",
  "returnFaceLandmarks": "true",
  "returnFaceAttributes": "age",
};

WebCamera.set({
  width: 640,
  height: 480,
  image_format: 'jpeg',
  jpeg_quality: 90,
  force_flash: false,
  fps: 45
});

let enabled = false;
let currentCapturedImage = null;
let userEmailAddress = "";

/* End of global parameters */

const validateEmail = function(email) {
    var re = /^(([^<>()\[\]\\.,;:\s@"]+(\.[^<>()\[\]\\.,;:\s@"]+)*)|(".+"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/;
    return re.test(email);
}

$.ajaxTransport("+binary", function (options, originalOptions, jqXHR) {
    // check for conditions and support for blob / arraybuffer response type
  if (window.FormData && ((options.dataType && (options.dataType == 'binary')) || (options.data && ((window.ArrayBuffer && options.data instanceof ArrayBuffer) || (window.Blob && options.data instanceof Blob))))) {
    return {
      // create new XMLHttpRequest
      send: function (headers, callback) {
          // setup all variables
        var xhr = new XMLHttpRequest(),
        url = options.url,
        type = options.type,
        async = options.async || true,
        // blob or arraybuffer. Default is blob
        dataType = options.responseType || "blob",
        data = options.data || null,
        username = options.username || null,
        password = options.password || null;

                xhr.addEventListener('load', function () {
                    var data = {};
                    data[options.dataType] = xhr.response;
                    // make callback and send data
                    callback(xhr.status, xhr.statusText, data, xhr.getAllResponseHeaders());
                });

                xhr.open(type, url, async, username, password);

                // setup custom headers
                for (var i in headers) {
                    xhr.setRequestHeader(i, headers[i]);
                }

                xhr.responseType = dataType;
                xhr.send(data);
            },
            abort: function () {
                jqXHR.abort();
            }
        };
  }
});

const logIn = function() {
  remote
    .getCurrentWindow()
    .loadURL(url.format({
      pathname: path.join(__dirname, "dashboard.pug"),
      protocol: 'file:',
      slashes: true
    }));
}

const getLocalImageId = function(blob=null) {
  return new Promise((resolve, reject) => {
    $.ajax({
      url: CONFIG.apiEndpoint + "detect?" + $.param(apiRequestParams),
      beforeSend: function(xhrObj){
        xhrObj.setRequestHeader("Content-Type","application/octet-stream");
        xhrObj.setRequestHeader("Ocp-Apim-Subscription-Key", CONFIG.apiKey);
      },
      type: "POST",
      data: blob || currentCapturedImage,
      processData: false
    })
    .done(function(data) {
        resolve(data);
    })
    .fail(function(err) {
        console.log(err)
        reject(err);
    });
  });
}

const getImageIdFromUrl = function(imageUrl) {
  return new Promise((resolve, reject) => {
    $.ajax(imageUrl, {
      dataType: "binary",
      processData: false
    })
    .done(function(data) {
      console.log(data)
      var blob = new Blob([data]);
      getLocalImageId(blob)
        .then(data => {
          resolve(data);
        })
        .catch(err => {
          reject(err);
        });
    });
  });
}

const startImageVerification = function() {
/*  getImageIdFromUrl("https://cyrex.southeastasia.cloudapp.azure.com/uploads/fristonio.jpg");*/
  let capturedImageFaceId = getLocalImageId();
  capturedImageFaceId.then(data => {
    console.log("First reuqest data", data)
    var firstImageId = data[0].faceId;
    var secondImageId = null;
    let serverUrl = CONFIG.serverRoot + "api/image";
    let payload = {
      email: userEmailAddress
    }
    $.ajax({
      url: serverUrl,
      type: "POST",
      beforeSend: function(xhrObj){
        xhrObj.setRequestHeader("Content-Type","application/json");
      },
      data: JSON.stringify(payload)
    })
    .done(function(data) {
      if (data) {
        console.log("I have all the data now", data);
        getImageIdFromUrl(CONFIG.serverRoot + "uploads/" + JSON.parse(data).image)
          .then(data => {
            console.log("Third request data", data)
            secondImageId = data[0].faceId;
            let imageVerificationParams = {
              faceId1: firstImageId,
              faceId2: secondImageId
            }
            console.log(imageVerificationParams)
            let verificationUrl = CONFIG.apiEndpoint + "verify";
            if (firstImageId && secondImageId) {
              $.ajax({
                url: verificationUrl,
                beforeSend: function(xhrObj){
                  xhrObj.setRequestHeader("Content-Type","application/json");
                  xhrObj.setRequestHeader("Ocp-Apim-Subscription-Key",CONFIG.apiKey);
                },
                type: "POST",
                data: JSON.stringify(imageVerificationParams),
                processData: false
              })
              .done(function(data) {
                if (data.isIdentical)
                  logIn();
              })
              .fail(function() {
                  alert("error");
              });
            }
          })
          .catch(err => {
            console.log("An error occured   ", err);
          });
      }
    });
  })
};

const dataURItoBlob = function(dataURI) {
  // convert base64 to raw binary data held in a string
  // doesn't handle URLEncoded DataURIs - see SO answer #6850276 for code that does this
  var byteString = atob(dataURI.split(',')[1]);

  // separate out the mime component
  var mimeString = dataURI.split(',')[0].split(':')[1].split(';')[0]

  // write the bytes of the string to an ArrayBuffer
  var ab = new ArrayBuffer(byteString.length);

  // create a view into the buffer
  var ia = new Uint8Array(ab);

  // set the bytes of the buffer to the correct values
  for (var i = 0; i < byteString.length; i++) {
      ia[i] = byteString.charCodeAt(i);
  }

  // write the ArrayBuffer to a blob, and you're done
  var blob = new Blob([ab], {type: mimeString});
  return blob;

}

let snapImage = function() {
  WebCamera.snap(function(data_uri) {
    currentCapturedImage = dataURItoBlob(data_uri);
    document.getElementById('webcam-capture-modal')
      .innerHTML = '<img src="'+data_uri+'"/>';
  });
}

/* Login button click */
document
  .querySelector("#login-button")
  .onclick = function() {
    console.log("INside quere")
    logIn();
  }

/* Login Form submit */
document
  .querySelector("#login-form")
  .onsubmit = function(e) {
    e.preventDefault();
    logIn();
}

/* Image Login homepage button click */
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

/* User Image modal click closing image capture div */
document
  .querySelector("#user-image-modal")
  .onclick = function(e) {
    e.stopPropagation();
    enabled = false;
    WebCamera.reset();
    document
      .querySelector("#capture-button")
      .style.display = "flex";
    document
      .querySelector("#image-upload-button")
      .style.display = "none";
    document
      .querySelector("#user-image-modal")
      .style.display = "none";
  }

/* Dummy function to stop button click event propogation */
document
  .querySelector(".image-button-container")
  .onclick = function(e) {
    e.stopPropagation();
  }

/* Captures the image using webcam js and paste it to img tag in image modal */
let captureImageAndPaste = function(e) {
    e.target.style.display = "none";
    document.querySelector("#image-upload-button")
      .style.display = "flex";
    snapImage();
  }

document
  .querySelector("#capture-button")
  .onclick = captureImageAndPaste;

document
  .querySelector("#image-upload-button")
  .onclick = function() {
    userEmailAddress = document.querySelector(".email-field-input").value;
    if (validateEmail(userEmailAddress) && currentCapturedImage)
      startImageVerification();
  }

document
  .querySelector(".email-field")
  .onclick = function(e) {
    e.stopPropagation();
  }

document
  .querySelector(".email-field")
  .onsubmit = function(e) {
    e.preventDefault();
    userEmailAddress = document.querySelector(".email-field-input").value;
    if (validateEmail(userEmailAddress) && currentCapturedImage)
      startImageVerification();
    alert('Capture a image and enter email address');
  }
