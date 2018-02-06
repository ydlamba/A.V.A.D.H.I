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

	@endif

@endsection
