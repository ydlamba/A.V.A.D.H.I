module.exports = {
  development: {
    dialect: "",
    storage: ""
  },
  test: {
    dialect: "",
    storage: ""
  },
  production: {
    username: process.env.DB_USERNAME,
    password: process.env.DB_PASSWORD,
    database: process.env.DB_NAME,
    host: process.env.DB_HOSTNAME,
    dialect: 'mysql',
  }
};
