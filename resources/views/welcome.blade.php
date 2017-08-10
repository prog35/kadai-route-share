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
        @include('routes.routes')
        {!! $routes->render() !!}
    @endsection
@endif