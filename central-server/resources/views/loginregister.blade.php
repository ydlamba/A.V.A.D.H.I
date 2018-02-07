<!Doctype html>
<html>
	<head>
		<link rel="stylesheet" href="{{asset('css/loginregister.css')}}">
		<link href="https://fonts.googleapis.com/css?family=Roboto:300,400,500" rel="stylesheet">
		<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css">
		<title>Login</title>
		<script src="https://code.jquery.com/jquery-1.9.1.min.js"></script>
	  	<link href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/2.0.1/css/toastr.css" rel="stylesheet"/>
	  	<script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/2.0.1/js/toastr.js"></script>
	</head>
	<body>
		<section class="main">
			<figure class="background-image"></figure>
			<section class="main-section">
				<header class="page-head">
					<h3 class="website-name">
						<span class="first-half">
							A.V.A.D.H.I
						</span> 
					</h3>
					<p class="description">
						<span class="first-letter">A</span>bsence <span class="first-letter">V</span>alidation and <span class="first-letter">D</span>etection <span class="first-letter">H</span>elping <span class="first-letter">I</span>nstrument
					</p>
				</header>
				<main class="form-container">
					<div class="login-form">
						<div class="form-head">
							<div class="form-details">
								<span class="form-heading">Login to our site</span>
								<span class="form-description">Enter username and password to log on</span>
							</div>
							<div class="head-icon">
								<i class="fa fa-lock"></i>
							</div>
						</div>
						<div class="form-body">
							<form action="/login" method="post">
								{{ csrf_field() }}
								<input type="text" name="username" placeholder="Username....">
								<input type="password" name="password" placeholder="Password....">
								<input type="submit">
							</form>
						</div>
					</div>
					<div class="vertical-rule">	
					</div>
					<div class="register-form">
						<div class="form-head">
							<div class="form-details">
								<span class="form-heading">Sign Up Now</span>
								<span class="form-description">Fill the form below to get instant access</span>
							</div>
							<div class="head-icon">
								<i class="fa fa-pencil"></i>
							</div>
						</div>
						<div class="form-body">
							<form action="/register" method="POST">
								{{ csrf_field() }}
								<input type="text" name="first_name" placeholder="First Name">
								<input type="text" name="last_name" placeholder="Last Name">
								<input type="text" name="username" placeholder="Username...">
								<input type="password" name="password" placeholder="Password...">
								<input type="text" name="email" placeholder="E-mail">
								<input type="submit">
							</form>
						</div>
					</div>
				</main>
			</section>	
		</section>
	@if (count($errors) != 0)
		<script>
			var errors = "{{ $errors }}"
			errors = errors.replace(/&quot;/g,'"')
			var errors_json = JSON.parse(errors);
			console.log(errors_json);
			var error_string = ""

			for(var key in errors_json)
			{
				error_string += (errors_json[key][0]+"\n");
			}

			toastr.error(error_string);
		</script>
	@endif
	@if ($message)
		<script type="text/javascript">
			var message = "{{ $message }}"
			toastr.success(message);
		</script>
	@endif
	@if ($login_error)
		<script type="text/javascript">
			var message = "{{ $login_error }}"
			toastr.error(message);
		</script>
	@endif

	</body>
</html>
