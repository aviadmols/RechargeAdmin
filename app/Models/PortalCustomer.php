<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class PortalCustomer extends Authenticatable
{
    use Notifiable;
    protected $fillable = [
        'email',
        'password',
        'recharge_customer_id',
        'first_name',
        'last_name',
        'last_synced_at',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'last_synced_at' => 'datetime',
            'password' => 'hashed',
        ];
    }
}
