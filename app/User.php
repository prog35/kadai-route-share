<?php

namespace App;

use Illuminate\Auth\Authenticatable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Auth\Passwords\CanResetPassword;
use Illuminate\Foundation\Auth\Access\Authorizable;
use Illuminate\Contracts\Auth\Authenticatable as AuthenticatableContract;
use Illuminate\Contracts\Auth\Access\Authorizable as AuthorizableContract;
use Illuminate\Contracts\Auth\CanResetPassword as CanResetPasswordContract;

class User extends Model implements AuthenticatableContract,
                                    AuthorizableContract,
                                    CanResetPasswordContract
{
    use Authenticatable, Authorizable, CanResetPassword;

    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'users';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['name', 'email', 'password'];

    /**
     * The attributes excluded from the model's JSON form.
     *
     * @var array
     */
    protected $hidden = ['password', 'remember_token'];
    

    public function routes()
    {
        return $this->belongsToMany(Route::class)->withPivot('type')->withTimestamps();
    }

    // ********************************************************
    // オーナールート
    // ********************************************************
    public function owner_routes()
    {
        return $this->routes()->where('type', 1);    // 1:owner
    }
    
    public function owner($routeId)
    {
        $exist = $this->is_owner($routeId);
        
        if ($exist) {
            return false;
        } else {
            $this->routes()->attach($routeId, ['type' => 1]);
            return true;
        }
    }
    
    public function is_owner($routeId)
    {
        return $this->owner_routes()->where('route_id', $routeId)->exists();
    }
    
    
    
    // ********************************************************
    // お気に入りルート
    // ********************************************************
    public function favorite_routes()
    {
        return $this->routes()->where('type', 2);    // 2:favo
    }
    
    public function favorite($routeId)
    {
        $exist = $this->is_favorite($routeId);
        
        if ($exist) {
            return false;
        } else {
            $this->routes()->attach($routeId, ['type' => 2]);
            return true;
        }
    }
    
    public function unfavorite($routeId)
    {
        $exist = $this->is_favorite($routeId);
        
        if ($exist) {
            \DB::delete("DELETE FROM user_route WHERE user_id = ? AND route_id = ? AND type = 2", [\Auth::user()->id, $routeId]);
            return true;
        } else {
            return false;
        }
    }
    
    public function is_favorite($routeId)
    {
        return $this->favorite_routes()->where('route_id', $routeId)->exists();
    }
    
    
    
}
