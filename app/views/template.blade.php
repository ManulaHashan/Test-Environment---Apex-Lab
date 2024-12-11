<!DOCTYPE html>
<html>
<head>
	<title>@yield('title')</title>
	<link href="css/bootstrap.min.css" rel="stylesheet">
	<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.1.1/jquery.min.js"></script>

	@yield('head')

</head>
<body style="margin: 0; padding: 0;">
	<h1 style="background-color: black; padding: 5px; color: white; margin-top: 0px; text-indent: 10px;">@yield('title')</h1>

	@yield('content')

</body>
</html>