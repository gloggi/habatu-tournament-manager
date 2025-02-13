<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Game extends Model
{
    use HasFactory;

    protected $guarded = [];

    protected $casts = [
        'played' => 'boolean',
    ];
    protected $appends = ['final_type_label'];
    protected $hidden = ['final_type'];

    public function getFinalTypeLabelAttribute()
    {
        $labels = [
            1   => 'Finale',
            2   => 'Halbfinale',
            4   => 'Viertelfinale',
            8   => 'Achtelfinale',
            16  => 'Achtundzwanzigstelfinale',
            32  => 'Zweiunddreißigstelfinale',
            64  => 'Vierundsechzigstelfinale',
            128 => 'Hundertachtundzwanzigstelfinale'
        ];

        return $labels[$this->final_type] ?? null;
    }

    public function teamA()
    {
        return $this->belongsTo(Team::class, 'team_a_id');
    }

    public function teamB()
    {
        return $this->belongsTo(Team::class, 'team_b_id');
    }

    public function hall()
    {
        return $this->belongsTo(Hall::class);
    }

    public function timeslot()
    {
        return $this->belongsTo(Timeslot::class);
    }

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function referees()
    {
        return $this->belongsToMany(User::class, 'game_user');
    }
}
