@extends('layouts.dashboard_admin')

@section('heading')
	Admin Panel
@endsection


@section('content')
	<div class="super-table-wrapper">
		<div class="admin-table-wrapper">
			<div class="admin-table-head">Online Users</div>
			<table class="mui-table admin-table">
			  <thead>
			    <tr>
			      <th> User </th>
			      <th> MAC Address</th>
			    </tr>
			  </thead>
			  <tbody>
			@foreach ($allOnline as $online)
			    <tr>
			      <td>{{$online->name}}</td>
			      <td>{{$online->mac_address}}</td>
			    </tr>
			@endforeach
			  </tbody>
			</table>
		</div>
		<div class="admin-table-wrapper">
			<div class="admin-table-head">Leaderboard</div>
			<table class="mui-table admin-table">
			  <thead>
			    <tr>
			      <th> User </th>
			      <th> MAC Address</th>
			      <th> Hours Spent </th>
			    </tr>
			  </thead>
			  <tbody>
			@foreach ($leaderboard as $person)
			    <tr>
			      <td>{{$person->name}}</td>
			      <td>{{$person->mac_address}}</td>
			      <td>{{$person->minutes/60}}</td>
			    </tr>
			@endforeach
			  </tbody>
			</table>
		</div>
	</div>
@endsection
