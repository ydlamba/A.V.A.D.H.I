// This loads the environment variables from the .env file
require('dotenv-extended').load();

var restify = require('restify');
var fs = require('fs');
var bot = require('./app/bot');
var models = require('./app/models/');

// Setup Restify Server on HTTPS
var server = restify.createServer({
  certificate: fs.readFileSync(process.env.PATH_CERT),
  key: fs.readFileSync(process.env.PATH_KEY)
});

server.listen(3978, '0.0.0.0', function () {
    console.log('%s listening to %s', server.name, server.url);
});

server.post('/api/messages', bot.connector('*').listen());
