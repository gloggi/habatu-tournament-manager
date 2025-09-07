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

    protected $appends = ['final_type_label', 'classes', 'has_referee'];

    protected $hidden = ['final_type'];

    public function getFinalTypeLabelAttribute()
    {
        $labels = [
            1 => 'Finale',
            2 => 'Halbfinale',
            4 => 'Viertelfinale',
            8 => 'Achtelfinale',
            16 => 'Achtundzwanzigstelfinale',
            32 => 'Zweiunddreißigstelfinale',
            64 => 'Vierundsechzigstelfinale',
            128 => 'Hundertachtundzwanzigstelfinale',
        ];

        if ($this->finale_type == 1 && $this->play_for_third) {
            return 'Spiele um den dritten Platz';
        }

        return $labels[$this->finale_type] ?? null;
    }

    public function getClassesAttribute()
    {
        $classes = [
            1 => 'final',
            2 => 'semifinal',
            4 => 'quarterfinal',
            8 => 'eighthfinal',
            16 => 'sixteenthfinal',
            32 => 'thirtysecondfinal',
            64 => 'sixtyfourthfinal',
            128 => 'hundredtwentyeighthfinal',
        ];
        if ($this->finale_type == 1 && $this->play_for_third) {
            return 'third-place';
        }

        return $classes[$this->finale_type] ?? '';
    }
    public function getHasRefereeAttribute()
    {
        return $this->referees()->exists();
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

    public function notifiedUsers()
    {
        return $this->belongsToMany(User::class, 'user_game_notification');
    }
}
