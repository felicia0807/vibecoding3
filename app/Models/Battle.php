<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Battle extends Model
{
    use HasFactory;

    protected $fillable = [
        'attacker_id',
        'defender_id',
        'winner_id',
        'coins_stolen',
    ];

    public function attacker()
    {
        return $this->belongsTo(User::class , 'attacker_id');
    }

    public function defender()
    {
        return $this->belongsTo(User::class , 'defender_id');
    }

    public function winner()
    {
        return $this->belongsTo(User::class , 'winner_id');
    }
}