<head>
	<title>{{ config('app.name', 'Laravel') }} @yield('title')</title>
	<meta charset="utf-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<meta name="csrf-token" content="{{ csrf_token() }}">
	<link href="{{ mix('/css/app.css') }}" rel="stylesheet">
	<script>
		window.Laravel = <?php echo json_encode([
			'csrfToken' => csrf_token(),
		]); ?>
	</script>
	<script src="https://use.fontawesome.com/68798271d7.js"></script>
	<link rel="icon" type="image/png" href="/images/idd_favicon.png" />
	<script src="https://ajax.googleapis.com/ajax/libs/jquery/2.2.4/jquery.min.js"></script>
</head>