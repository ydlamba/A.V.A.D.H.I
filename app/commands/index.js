const rp = require('request-promise');
let url = 'https://cyrex.southeastasia.cloudapp.azure.com';

module.exports = {
  botOnline: function () {
    return rp(url +  '/api/onlineusers');
  },
  botStats: function () {
    return rp(url + '/api/leaderboard');
  }
}
