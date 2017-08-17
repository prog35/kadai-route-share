@extends('layouts.app')

@if (Auth::check() == false)
    @section('cover')
        <div class="cover">
            <div class="cover-inner">
                <div class="cover-contents">
                    <h1>おすすめルートをシェアしよう</h1>
                    @if (!Auth::check())
                        <a href="{{ route('signup.get') }}" class="btn btn-success btn-lg">ルートシェアを始める</a>
                    @endif
                </div>
            </div>
        </div>
    @endsection
    
@else

    @section('content')
        <div class='row'>
            <div class="text-center">
                {!! Form::open(['route' => 'routes.index', 'method' => 'get', 'class' => 'form-inline']) !!}
                    <div class="input-group" id='searchbox'>
                        {!! Form::text('keyword', $keyword, ['class' => 'form-control input-lg', 'placeholder' => 'キーワードを入力', 'size' => 40]) !!}
                        <span class="input-group-btn">
                        {!! Form::submit("検索", ['class' => 'btn btn-default btn-lg']) !!}
                        </span>
                    </div>
                {!! Form::close() !!}
            </div>
        </div>
        
        @include('routes.routes')
        <div class="text-center">
            {!! $routes->render() !!}
        </div>
    @endsection
@endif