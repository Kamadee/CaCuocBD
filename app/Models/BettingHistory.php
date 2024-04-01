<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BettingHistory extends Model
{
    use HasFactory;
    protected $table = 'betting_histories'; 

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
