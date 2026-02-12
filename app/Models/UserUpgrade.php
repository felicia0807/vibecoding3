<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserUpgrade extends Model
{
    use HasFactory;

    protected $fillable = ['user_id', 'upgrade_type', 'level'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // Cost calculation logic could go here
    public function getCostAttribute(): int
    {
        // Base costs for upgrades
        $baseCosts = [
            'miner' => 10,
            'sword' => 50,
            'wall' => 50,
            'shield' => 100,
            'auto_miner' => 500,
        ];

        $base = $baseCosts[$this->upgrade_type] ?? 10;
        // Cost increases by 50% each level
        return (int)($base * pow(1.5, $this->level));
    }
}