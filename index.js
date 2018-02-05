// This loads the environment variables from the .env file
require('dotenv-extended').load();

var restify = require('restify');
var bot = require('./app/bot');
var models = require('./app/models/');

// Setup Restify Server
var server = restify.createServer();
server.listen(process.env.port || process.env.PORT || 3978, function () {
    console.log('%s listening to %s', server.name, server.url);
});

server.post('/api/messages', bot.connector('*').listen());
