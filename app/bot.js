'use strict';
const builder = require('botbuilder');
const models = require('./models/');

const connector = new builder.ChatConnector({
    appId: process.env.MICROSOFT_APP_ID,
    appPassword: process.env.MICROSOFT_APP_PASSWORD
});

const bot = module.exports = new builder.UniversalBot(connector, function (session) {
  var reply = new builder.Message()
      .address(session.message.address);

  var text = session.message.text.toLocaleLowerCase();

  console.log('[' + session.message.address.conversation.id + '] Message received: ' + text);

  switch (text) {
      case 'bot stats':
          reply.text('Here are the stats.')
          break;

      case 'bot show online':
          reply.text('Online Users are:')
          break;

      default:
          reply.text('Please! I am still learning.');
          break;
  }

  session.send(reply);

});

