<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BettingHistory extends Model
{
    use HasFactory;

    protected $fillable = [
        'id',
        'user_id',
        'match_id',
        'choice',
        'money_bet',
        'money_receive',
        'created_at',
        'updated_at',
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
    public function match()
    {
        return $this->belongsTo(MatchModel::class, 'match_id');
        
    }

    public function setMoneyReceive($value)
    {
        $this->attributes['moneyReceive'] = $value;
    }
}
