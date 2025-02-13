<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Option extends Model
{
    use HasFactory;

    protected $guarded = [];

    protected $casts = [
        'start_time' => 'datetime:H:i',
        'round_robin' => 'boolean',
        'group_phase' => 'boolean',
        'play_for_third_place' => 'boolean',
    ];
}
