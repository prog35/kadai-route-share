@if (Auth::user()->is_favorite($route->id))
    {!! Form::open(['route' => ['users.unfavorite'], 'method' => 'delete']) !!}
        {!! Form::hidden('route_id', $route->id) !!}
        {!! Form::submit('UnFavorite', ['class' => "btn btn-default btn-xs"]) !!}
    {!! Form::close() !!}
@else
    {!! Form::open(['route' => ['users.favorite'], 'method' => 'post']) !!}
        {!! Form::hidden('route_id', $route->id) !!}
        {!! Form::submit('Favorite', ['class' => "btn btn-default btn-xs"]) !!}
    {!! Form::close() !!}
@endif
