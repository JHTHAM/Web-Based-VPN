<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Network extends Model
{
    protected $fillable = ['name'];

    public function routers() {
        return $this->belongsToMany(Router::class, 'network_router');
    }
}
