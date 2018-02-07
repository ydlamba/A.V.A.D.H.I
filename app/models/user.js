'use strict';
module.exports = (sequelize, DataTypes) => {
  var User = sequelize.define('users', {
    name: DataTypes.STRING
  }, {
    timestamps: false
  });

  return User;
};
