<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class IPAddress extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'ip_addresses';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name',
        'v4',
    ];
}
