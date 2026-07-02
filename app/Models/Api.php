<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Api extends Model
{
    protected $table = 'apis';
    // Allow mass assignment for these fields
    protected $fillable = [
        'type',
        'name',
        'url',
        'key',
        'status',
    ];
}
