@extends('layouts.dashboard_admin')

@section('content')
<div id="bot">
</div>

<script src="https://cdn.botframework.com/botframework-webchat/latest/botchat.js"></script>
    <script src="https://cdn.botframework.com/botframework-webchat/latest/CognitiveServices.js"></script>
    <script>
      var speechOptions = {
        speechRecognizer: new CognitiveServices.SpeechRecognizer( { subscriptionKey: '485a9ca522584e2683c1f2acb202c9c9' } ),
        speechSynthesizer: new CognitiveServices.SpeechSynthesizer(
        {
          subscriptionKey: '485a9ca522584e2683c1f2acb202c9c9',
          gender: CognitiveServices.SynthesisGender.Female,
          voiceName: 'Microsoft Server Speech Text to Speech Voice (en-US, JessaRUS)'
        })
      }

      BotChat.App({
        directLine: { secret: 'm-Yb4WT5gOo.cwA.dgo.e3TEGxGQejL33plPTNzjML-seN4JCEf_7zy6630K9u4' },
        user: { id: 'User' },
        bot: { id: 'ApnaSaathi' },
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
