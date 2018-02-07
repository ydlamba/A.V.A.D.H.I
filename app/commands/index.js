const rp = require('request-promise');
const url = 'https://cyrex.southeastasia.cloudapp.azure.com';

module.exports = {
  botOnline: function () {
    return rp(url +  '/api/onlineusers');
  },
  botStats: function () {
    return rp(url + '/api/leaderboard');
  }
}
