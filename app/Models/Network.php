<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Network extends Model
{
    protected $fillable = ['name'];

    public function routers() {
        return $this->belongsToMany(Router::class, 'network_router');
    }

    public function standaloneClients()
    {
        return $this->belongsToMany(StandaloneClient::class, 'network_standalone_client');
    }
}
