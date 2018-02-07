const models = require('../models/');

module.exports = {
  botOnline: function () {
    return models.users.findAll();
  },
  botStats: function () {
    return models.logs.findAll({
      where: {
        timestamp: {
          [Op.lt]: [current]
        }
      }
    });
  }
}
