@if ($routes)
    <div class="row">
        @foreach ($routes as $key => $route)
            <div class="route">
                <div class="col-md-6 col-sm-6 col-xs-12">
                    <div class="panel panel-default">
                        <div class="panel-heading text-center">
                            <a href="{{ route('routes.show',$route->id) }}"><img src="{{ $route->static_map_url }}" class="img-responsive img-thumbnail"></a>
                        </div>
                        <div class="panel-body">
                            <p class="route-description">{{ $route->description }}</p>
                            
                        </div>
                        <div class="panel-footer">
                            <div class="buttons text-center">
                                @include('user_favorite.favorite_button')
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        @endforeach
    </div>
@endif