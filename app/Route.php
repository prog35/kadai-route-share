<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Route extends Model
{
    public function routeDetail()
    {
        return $this->hasMany(RouteDetail::class);
    }
    
    public function users()
    {
        return $this->belongsToMany(User::class)->withPivot('type')->withTimestamps();
    }
    
    
}
