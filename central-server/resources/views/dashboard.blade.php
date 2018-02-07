@extends('layouts.dashboard_template')

@section('heading')

	Overview

@endsection

@section('content')
	
	@if($user->mac_address == null)

		You haven't registered a MAC yet, Register Below.	

		<form class="mui-form mac-form" action="/user/mac" method="POST" enctype="multipart/form-data">
		  {{ csrf_field() }}
		  <div class="mui-textfield mui-textfield--float-label">
		    <input type="text" name="mac_address">
		    <label>MAC Address</label>
		  </div>
		  <div class="fileUpload">
		  	<input type="file" name="image" id="file">
		  </div> 
		  <button type="submit" class="mui-btn mac-submit mui-btn--raised">Submit</button>
		</form>
	@else
		<div class="overview-container"> 
			<span class="overview-label"> Username: </span> <span class="overview-data">{{ $user->name }}</span>
		</div>
		<div class="overview-container">
				<span class="overview-label"> MAC:</span> <span class="overview-data overview-data--mac">{{$user->mac_address}}</span>
		</div>	
		<div class="overview-container">
			@if ($user->isOnline())
				<span class="overview-label">Status: </span> <span class="overview-data"> Online </span>
			@else
				<span class="overview-label"> Last Seen:</span> <span class="overview-data">{{$user->lastSeen()}}</span>
			@endif
		</div>
	@endif

@endsection
