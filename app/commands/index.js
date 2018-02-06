const models = require('../models/');

module.exports = {
  botOnline: function () {
    return models.User.findAll();
  }
}
