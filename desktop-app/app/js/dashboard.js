const WebCamera = require("webcamjs");
const CONFIG = require("./config/config.js");
const toastr = require("toastr");
const { remote } = require("electron");
const path = require("path");
const url = require("url");

const userEmailAddress = window.localStorage.getItem('email');
const lastLoggedIn = window.localStorage.getItem('lastLogin');
console.log(userEmailAddress, lastLoggedIn);
const MAX_RETRIES = 3;
let loginRetries = 0;

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

$.ajaxTransport("+binary", function (options, originalOptions, jqXHR) {
    // check for conditions and support for blob / arraybuffer response type
  if (window.FormData &&
    ((options.dataType &&
      (options.dataType == 'binary'))
        || (options.data &&
          ((window.ArrayBuffer &&
            options.data instanceof ArrayBuffer)||
          (window.Blob && options.data instanceof Blob)
        )
      )
    )) {
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

const logOut = function(force=false) {
  if (loginRetries >= MAX_RETRIES || force) {
    console.log("USER Is loggin out ... log in again")
    window.localStorage.setItem('email', "");
    window.localStorage.setItem('lastLogin', "");
    remote
      .getCurrentWindow()
      .loadURL(url.format({
        pathname: path.join(__dirname, "index.pug"),
        protocol: 'file:',
        slashes: true
      }));
  }
  else {
    console.log("Running face check again");
    loginRetries++;
    runFaceCheck();
  }

};


const getLocalImageId = function(blob) {
  return new Promise((resolve, reject) => {
    $.ajax({
      url: CONFIG.apiEndpoint + "detect?" + $.param(apiRequestParams),
      beforeSend: function(xhrObj){
        xhrObj.setRequestHeader("Content-Type","application/octet-stream");
        xhrObj.setRequestHeader("Ocp-Apim-Subscription-Key", CONFIG.apiKey);
      },
      type: "POST",
      data: blob,
      processData: false
    })
    .done(function(data) {
      if(data)
        resolve(data);
      else {
        console.log("NO DATA while getting faceId from image BLOB", err)
        logOut();
      }
    })
    .fail(function(err) {
        console.log("ERROR while getting faceId from image BLOB", err)
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
      if (data) {
        var blob = new Blob([data]);
        getLocalImageId(blob)
          .then(data => {
            resolve(data);
          })
          .catch(err => {
            reject(err);
          });
      } else {
        console.log("NO DATA when fetching faceId from imageURL", err);
        logOut();
      }
    })
    .fail(function(err) {
      console.log("ERROR when fetching faceId from imageURL", err);
      logOut();
    });
  });
}


let getUserImageFromServer = function() { 
  let serverUrl = CONFIG.serverRoot + "api/image";
  let payload = {
    email: userEmailAddress
  }
  return new Promise((resolve, reject) => { 
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
        getImageIdFromUrl(
          CONFIG.serverRoot + "uploads/" + JSON.parse(data).image
        )
        .then(data =>{
          resolve(data);
        });
      } else {
        toastr.warning("No data from server for user email image");
        reject("Data is empty when getting image name from server")
      }
    })
    .fail(function(data) {
      console.log("ERROR occured while fetching user image");
      reject("ERROR occured while fetching user image");
    });
  })
}

const verifyImageIds = function(imageVerificationParams) {
  let verificationUrl = CONFIG.apiEndpoint + "verify";
  console.log("Got image Ids :: ", imageVerificationParams);
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
    console.log("Data on comparing is :: ", data);
    if (data.isIdentical) {
      console.log("Login verified");
      loginRetries = 0;
    } else {
      console.log("Login cannot be verified ....")
      logOut();
    }
  })
  .fail(function(err) {
      console.log("Error when comparing image face IDS", err);
      logOut();
  });
}

const runFaceCheck = function() {
  getUserImageFromServer()
    .then(data => {
      let serverImageId = data[0].faceId;
      document.querySelector("#webcam-capture-modal")
        .innerHTML = "";
      WebCamera.attach('#webcam-capture-modal');
      WebCamera.on('live', function() {
        setTimeout(function() {
          WebCamera.snap(function(data_uri) {
            WebCamera.reset();
            if(!data_uri) {
              toastr.warning("No image captured from webcam");
            }
            recentCapturedImage = dataURItoBlob(data_uri);
            getLocalImageId(recentCapturedImage)
              .then(data => {
                let localImageId = data[0].faceId;
                verifyImageIds({
                  faceId1: localImageId,
                  faceId2: serverImageId
                })
              })
              .catch(err => {
                logOut();
              })
          });
        }, 5000);
      });
    })
    .catch(err => {
      logOut();
    })

}

document.querySelector(".logout-button")
  .onclick = function() {
    logOut(true);
  }

runFaceCheck();

WebCamera.on('error', function(err) {
  toastr.warning("Some problem with webcam ... Trying again");
})

setInterval(_ => {
  runFaceCheck()
}, 1000*30*60);
