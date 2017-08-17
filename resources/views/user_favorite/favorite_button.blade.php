@if (Auth::user()->is_owner($route->id))
    Route Created
@else
    @if (Auth::user()->is_favorite($route->id))
        {!! Form::open(['route' => ['users.unfavorite'], 'method' => 'delete']) !!}
            {!! Form::hidden('route_id', $route->id) !!}
            {!! Form::submit('お気に入りから削除', ['class' => "btn btn-default btn-xs"]) !!}
        {!! Form::close() !!}
    @else
        {!! Form::open(['route' => ['users.favorite'], 'method' => 'post']) !!}
            {!! Form::hidden('route_id', $route->id) !!}
            {!! Form::submit('お気に入り登録', ['class' => "btn btn-default btn-xs"]) !!}
        {!! Form::close() !!}
    @endif
@endif