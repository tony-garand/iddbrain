<!DOCTYPE html>
<html lang="en" class="brand_{{ Session::get('user_brand') }}">
@include('shared.'.Session::get('user_brand').'_head')

<body>

	<div id="app">
		@if (env('IS_DEV_MODE')=='1')
			<div class="warning_lbl_top">
				<div class="container">
					Warning: You're on the development server - go to <a href="http://brain.iddigital.us/" target="_new">Production</a>.
				</div>
			</div>
		@endif

		<nav class="navbar navbar-default navbar-static-top">
			<div class="container">
				<div class="navbar-header">
					<button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#app-navbar-collapse">
						<span class="sr-only">Toggle Navigation</span>
						<span class="icon-bar"></span>
						<span class="icon-bar"></span>
						<span class="icon-bar"></span>
					</button>
					<a class="navbar-brand" href="{{ url('/') }}">
						<center><img src="/images/{{ Session::get('user_brand') }}_logo.png" width="110" /></center>
					</a>
				</div>
				@include('shared.menu')
			</div>
		</nav>

		@yield('content')

	</div>

	@include('shared.footer')

	<script src="{{ mix('/js/app.js') }}"></script>
</body>
</html>