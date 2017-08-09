@extends('layouts.app')

@section('content')
    <div class="user-profile">
        <div class="icon text-center">
            <img src="{{ Gravatar::src(Auth::user()->email, 100) . '&d=mm' }}" alt="" class="img-circle">
        </div>
        <div class="name text-center">
            <h1>{{ $user->name }}</h1>
        </div>
        <div class="status text-center">
            <ul>
                <li>
                    <div class="status-label">作成</div>
                    <div id="owner_count" class="status-value">
                        {{ $count_owner }}
                    </div>
                </li>
                <li>
                    <div class="status-label">お気に入り</div>
                    <div id="favorite_count" class="status-value">
                        {{ $count_favorite }}
                    </div>
                </li>
            </ul>
        </div>
    </div>
    @include('routes.routes')
    {!! $items->render() !!}
@endsection

