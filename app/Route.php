<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Route extends Model
{
    protected $fillable = ['description', 'status', 'static_map_url','zoom','center_lat','center_lng'];

    public function routeDetail()
    {
        return $this->hasMany(RouteDetail::class);
    }
    
    public function users()
    {
        return $this->belongsToMany(User::class)->withPivot('type')->withTimestamps();
    }
    
    
}
