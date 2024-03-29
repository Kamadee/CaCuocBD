<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MatchModel extends Model
{
    use HasFactory;

    protected $fillable = [
        'home_id',
        'away_id',
        'match_start_time',
        'match_end_time',
        'stop_bet_time',
        'betting_odds',
        'result',
        'is_public'
    ];
    protected $table = 'matches';

    public function home()
    {
        return $this->belongsTo(Club::class, 'id', 'home_id');
    }

    public function away()
    {
        return $this->belongsTo(Club::class, 'id', 'away_id');
    }
    public function bettingHistory()
    {
        return $this->hasMany(Club::class, 'match_id', 'id');
    }
    
}