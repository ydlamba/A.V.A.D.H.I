'use strict';
module.exports = (sequelize, DataTypes) => {
  var Log = sequelize.define('logs', {
    mac_address: DataTypes.STRING,
    ip_address: DataTypes.STRING,
    timestamp: DataTypes.DATE,
  }, {
    timestamps: false
  });

  return Log;
};
