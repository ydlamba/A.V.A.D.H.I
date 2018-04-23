<!DOCTYPE html>
<html>
<head>
	<title>Admin</title>
	<link href="https://fonts.googleapis.com/css?family=Roboto:300,400,500" rel="stylesheet">
	<link href="https://fonts.googleapis.com/css?family=Ubuntu" rel="stylesheet">
	<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css">
	<link href="//cdn.muicss.com/mui-0.9.36/css/mui.min.css" rel="stylesheet" type="text/css" />
	<script src="//cdn.muicss.com/mui-0.9.36/js/mui.min.js"></script>
	<script src="https://code.jquery.com/jquery-1.9.1.min.js"></script>
  	<link href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/2.0.1/css/toastr.css" rel="stylesheet"/>
  	<script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/2.0.1/js/toastr.js"></script>
  	<script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.1.4/Chart.bundle.min.js"></script>
	<title>Login</title>
	<link rel="stylesheet" href="{{asset('css/dashboard.css')}}">
	<link href="https://cdn.botframework.com/botframework-webchat/latest/botchat.css" rel="stylesheet" />
</head>
<body>
	<section class="main">
		<header class="page-head">
			<a href="/dashboard" class="website-name"><h4><span class="first-letter">A</span>.V.A.D.H.I</h4></a>
			<div class="right-nav">
				<a class="profile-link" href="/dashboard"> <img src="{{ asset('/images/profile.jpg')}}" class="profile-pic"> </a>
				<a href="/logout">
					<i class="fa fa-sign-out logout" aria-hidden="true"></i>
				</a>
			</div>
		</header>	
		<main> 
			<nav class="left-nav"> 
				<ul class="main-nav">
					<li class="nav-head">
						MAIN 
					</li>
					<li class="nav-item"> 
						<a href="/admin" class="nav-link">
							<i class="fa fa-rss" aria-hidden="true"></i>
							<div> Activity </div>
						</a>
					</li>
				</ul>
				<ul class="filter-nav">
					<li class="nav-head">
						<div>FILTER</div>
					</li> 
					<li class="nav-item"> 
						<a href="/admin/stats" class="nav-link">
							<i class="fa fa-bar-chart-o" aria-hidden="true"></i>
							<div> Time Scores </div>
						</a>	
					</li>
					<li class="nav-item"> 
						<a href="/admin/sathi" class="nav-link">
							<i class="fa fa-pie-chart" aria-hidden="true"></i>
							<div> Saathi </div>
						</a>
					</li>
					<li class="nav-item">
						<a href="/logout" class="nav-link">
							<i class="fa fa-sign-out" aria-hidden="true"></i>
							<div> Logout </div>	
						</a>
					</li>
				</ul>
			</nav>
			<section class="user-space">
				<div class="user-space-head"> @yield('heading') </div>
				<div class="user-content">
					@yield('content')
				</div>
			</section>
		</main>
	</section>
	@if ($message)
		<script type="text/javascript">
			var message = "{{ $message }}"
			toastr.success(message);
		</script>
	@endif
	@if ($error)
		<script type="text/javascript">
			var message = "{{ $error }}"
			toastr.error(message);
		</script>
	@endif
</body>
</html>
