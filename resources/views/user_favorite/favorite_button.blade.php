@if (Auth::user()->is_favorite($routes->id))
    {!! Form::open(['route' => ['$routes.unfavorite', $routes->id], 'method' => 'delete']) !!}
        {!! Form::submit('UnFavorite', ['class' => "btn btn-default btn-xs"]) !!}
    {!! Form::close() !!}
@else
    {!! Form::open(['route' => ['user.favorite', $routes->id]]) !!}
        {!! Form::submit('Favorite', ['class' => "btn btn-default btn-xs"]) !!}
    {!! Form::close() !!}
@endif

    