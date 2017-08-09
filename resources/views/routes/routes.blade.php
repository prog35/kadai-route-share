@if ($routes)
    <div class="row">
        @foreach ($routes as $key => $route)
            <div class="route">
                <div class="col-md-3 col-sm-4 col-xs-12">
                    <div class="panel panel-default">
                        <div class="panel-heading text-center">
                            スタティックルートを表示
                        </div>
                        <div class="panel-body">
                            <p class="route-title"><a href="{{ route('items.show', $route->id) }}">{{ $route->description }}</a></p>
                            <div class="buttons text-center">
                                <!--@include('items.want_button', ['route' => $route])-->
                                <!--@include('items.have_button', ['route' => $route])-->
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        @endforeach
    </div>
@endif