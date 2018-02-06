'use strict';
const builder = require('botbuilder');;
const commands = require('./commands')

const connector = new builder.ChatConnector({
    appId: process.env.MICROSOFT_APP_ID,
    appPassword: process.env.MICROSOFT_APP_PASSWORD
});

const bot = module.exports = new builder.UniversalBot(connector, function (session) {
  var reply = '';
  var message = session.message;
  var conversationId = message.address.conversation.id;
  var text = message.text.toLocaleLowerCase();
  var response = [];
  console.log('[' + conversationId + '] Message received: ' + text);

  switch (text) {
      case 'bot stats':
          reply = 'Here are the stats:';
          session.send(reply);
          break;

      case 'bot online':
          commands.botOnline().then(function (data) {
            if (data) { 
              data.forEach(function (user) {
                response.push(user.dataValues.name + '\n')
              });
            } else {
              console.log('Error Occured.')
            }
            reply = response.join('\r\n');
            session.send('ONLINE USERS: <br>' + reply);
          });
          break;

      default:
          reply = 'Please! I am still learning.';
          break;
  }

});

