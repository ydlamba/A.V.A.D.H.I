<!DOCTYPE html>
<html>
<head>
	<title>Dashboard</title>
	<link href="https://fonts.googleapis.com/css?family=Roboto:300,400,500" rel="stylesheet">
	<link href="https://fonts.googleapis.com/css?family=Ubuntu" rel="stylesheet">
	<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css">
	<link href="//cdn.muicss.com/mui-0.9.36/css/mui.min.css" rel="stylesheet" type="text/css" />
	<script src="//cdn.muicss.com/mui-0.9.36/js/mui.min.js"></script>
	<link rel="stylesheet" href="{{asset('css/dashboard.css')}}">
	<script src="http://code.jquery.com/jquery-1.9.1.min.js"></script>
  	<link href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/2.0.1/css/toastr.css" rel="stylesheet"/>
  	<script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/2.0.1/js/toastr.js"></script>
  	<script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.1.4/Chart.bundle.min.js"></script>
	<title>Login</title>
</head>
<body>
	<section class="main">
		<header class="page-head">
			<div class="website-name"><h3><span class="first-letter">A</span>.V.A.D.H.I</h3></div>
			<div class="right-nav">
				<img src="{{ asset('/images/profile.jpg')}}" class="profile-pic">
				<form method="post" class="search-bar"><input type="text"></form>
			</div>
		</header>	
		<main> 
			<nav class="left-nav"> 
				<ul class="main-nav">
					<li class="nav-head">
						MAIN 
					</li>
					<li class="nav-item"> 
						<a href="/dashboard" class="nav-link">
							<i class="fa fa-user-circle-o" aria-hidden="true"></i>
							<div> Overview </div>
						</a>	
					</li>
					<li class="nav-item"> 
						<a href="#" class="nav-link">
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
						<a href="#" class="nav-link">
							<i class="fa fa-star-half-o" aria-hidden="true"></i>
							<div> Predictions </div>
						</a>
					</li>
					<li class="nav-item"> 
						<a href="/dashboard/bar" class="nav-link">
							<i class="fa fa-bar-chart-o" aria-hidden="true"></i>
							<div> Bar Graph </div>
						</a>	
					</li>
					<li class="nav-item"> 
						<a href="#" class="nav-link">
							<i class="fa fa-pie-chart" aria-hidden="true"></i>
							<div> Pie Chart </div>
						</a>
					</li>
					<li class="nav-item">
						<a href="#" class="nav-link">
							<i class="fa fa-line-chart" aria-hidden="true"></i>
							<div> Line Chart </div>	
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
