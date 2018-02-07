const WebCamera = require("webcamjs");
const CONFIG = require("./config/config.js");

const userEmailAddress = window.localStorage.getItem('email');
const lastLoggedIn = window.localStorage.getItem('last-login');
console.log(userEmailAddress, last-login);
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

const logOut();


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
      return getImageIdFromUrl(
        CONFIG.serverRoot + "uploads/" + JSON.parse(data).image
      );
    }
  })
  .fail(function(data) {
    console.log("ERROR occured while fetching user image")
    logOut()
    return new Promise((resolve, reject) => reject("ERROR"));
  });
}

const verifyImageIds = function(imageVerificationParams) {
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
    if (data.isIdentical) {
      console.log("Login verified");
      loginRetries = 0;
    } else {
      console.log("Login cannot be verified ....")
      logOut();
    }
  })
  .fail(function() {
      logOut();
  });
}

const runFaceCheck = function() {
  getUserImageFromServer()
    .then(data => {
      let serverImageId = data[0].faceId;
      WebCamera.snap(function(data_uri) {
        recentCapturedImage = dataURItoBlob(data_uri);
        document.getElementById('webcam-capture-modal')
          .innerHTML = '<img src="'+data_uri+'"/>';
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
    })
    .catch(err => {
      logOut();
    })

}

setTimeout(runFaceCheck(), 30*60);
