<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class RouteDetail extends Model
{
    protected $table = 'route_detail';
    protected $fillable = ['route_id', 'latitude','longitude'];

    public function route()
    {
        return $this->belongsTo(Route::class);
    }
}
