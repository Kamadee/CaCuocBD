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
        return $this->belongsTo(Club::class, 'home_id');
    }

    public function away()
    {
        return $this->belongsTo(Club::class, 'away_id');
    }

    public function bettingHistories()
    {
        return $this->hasMany(BettingHistory::class, 'match_id');
    }
    
}