<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Router extends Model
{
    protected $fillable = [
        'name',
        'ip_address',
        'label',
        'status',
        'ovpn_path',
    ];

    public function networks() {
        return $this->belongsToMany(Network::class, 'network_router');
    }
}
