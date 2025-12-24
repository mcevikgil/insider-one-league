<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Team extends Model
{
    protected $fillable = [
        'name',
        'short_name',
        'strength',
        'attack',
        'defense',
        'squad_deft',
        'form',
        'is_selected'
    ];

    protected $casts = [
        'is_selected' => 'boolean'
    ];
}
