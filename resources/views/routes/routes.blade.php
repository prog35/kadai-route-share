@if ($routes)
    <div class="row">
        @foreach ($routes as $key => $route)
            <div class="route">
                <div class="col-md-6 col-sm-6 col-xs-12">
                    <div class="panel panel-default">
                        <div class="panel-heading text-center">
                            <img src="{{ $route->polylin_latlon }}" class="img-responsive">
                        </div>
                        <div class="panel-body">
                            <p class="route-title"><a href="#">{{ $route->description }}</a></p>
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