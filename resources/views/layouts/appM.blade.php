<!DOCTYPE html>
<html>
    <head>
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <title>RouteShare</title>

	<script type="text/javascript" src="http://ajax.googleapis.com/ajax/libs/jquery/1.7/jquery.min.js" ></script>
	<script src="http://maps.google.com/maps/api/js?key={{ env('GOOGLE_MAPS_JS_ID') }}" type="text/javascript"></script>
	<script type="text/javascript" src="{{ asset('js/map.js') }}"></script>
	<script type="text/javascript" src="{{ asset('js/jquery.keybind.js') }}"></script>
   
        <link rel="stylesheet" href="{{ asset('/css/style.css') }}">

    </head>
    <body>

        <div class="container">
            @include('commons.error_messages')
            @yield('content')
        </div>


    </body>
</html>