'use strict';
module.exports = (sequelize, DataTypes) => {
  var User = sequelize.define('User', {
    name: DataTypes.STRING,
    createdAt: DataTypes.DATE,
    updatedAt: DataTypes.DATE
  });

/* User.associate = function(models) {
    models.User.hasMany(models.Task);
  };*/

  return User;
};
