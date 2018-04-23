@extends('layouts.dashboard_admin')

@section('content')
<div id="bot">
</div>

<script src="https://cdn.botframework.com/botframework-webchat/latest/botchat.js"></script>
    <script src="https://cdn.botframework.com/botframework-webchat/latest/CognitiveServices.js"></script>
    <script>
      var speechOptions = {
        speechRecognizer: new CognitiveServices.SpeechRecognizer( { subscriptionKey: '4657c33affc149708a159baab067d49a' } ),
        speechSynthesizer: new CognitiveServices.SpeechSynthesizer(
        {
          subscriptionKey: 'c05e7de53aef4dc1bc7224f475b5a407',
          gender: CognitiveServices.SynthesisGender.Female,
          voiceName: 'Microsoft Server Speech Text to Speech Voice (en-US, JessaRUS)'
        })
      }

      BotChat.App({
        directLine: { secret: 'dVnstM7leio.cwA.laQ.00qD5acGB1VWZmFs5H8KMQe3YYGYKIJpNQtkEW3ImpM' },
        user: { id: 'User' },
        bot: { id: 'Saathi' },
        resize: 'detect',
        speechOptions: speechOptions
      }, document.getElementById("bot"));
    </script>
    <style>
        .user-space{
            width: 75%;
            height: 85%;
        }
    </style>

	
@endsection
