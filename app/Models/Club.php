<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Club extends Model
{
    use HasFactory;
    protected $table = 'clubs';
    public function match()
    {
        return $this->hasMany(MatchModel::class, 'home_id', 'id')->orWhere('away_id', $this->id);
    }
}
