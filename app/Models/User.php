<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'coins',
        'power',
        'defense',
        'last_defended_at',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'last_defended_at' => 'datetime',
        ];
    }

    public function upgrades()
    {
        return $this->hasMany(UserUpgrade::class);
    }

    public function battlesAsAttacker()
    {
        return $this->hasMany(Battle::class , 'attacker_id');
    }

    public function battlesAsDefender()
    {
        return $this->hasMany(Battle::class , 'defender_id');
    }

    // Helper to get specific upgrade level
    public function getUpgradeLevel(string $type): int
    {
        return $this->upgrades()->where('upgrade_type', $type)->value('level') ?? 0;
    }

    // Calculate total click power (Base 1 + Miner Level)
    public function getClickPowerAttribute(): int
    {
        $minerLevel = $this->getUpgradeLevel('miner');
        $base = 1;
        // Example logic: Level 1 = +1, Level 10 = +10.
        // VIP miner (+25) logic could be added here if 'vip' was an upgrade type.
        return $base + $minerLevel;
    }

    // Calculate total attack power (Base Power + Sword Level)
    public function getTotalAttackPowerAttribute(): int
    {
        $swordLevel = $this->getUpgradeLevel('sword');
        return $this->power + $swordLevel;
    }

    // Calculate total defense (Base Defense + Wall Level + Shield Level)
    public function getTotalDefenseAttribute(): int
    {
        $wallLevel = $this->getUpgradeLevel('wall');
        $shieldLevel = $this->getUpgradeLevel('shield');

        // Simple logic: Wall gives raw defense. Shield might be special logic (block 1 attack).
        // For 'stats' comparison, we sum them up.
        return $this->defense + $wallLevel + ($shieldLevel * 5); // Assuming shield adds significant defense value
    }

    // Auto Miner production per minute
    public function getAutoMinerProductionAttribute(): int
    {
        $autoMinerLevel = $this->getUpgradeLevel('auto_miner');
        // Example: Level 1 -> 50, Level 5 -> 500. 
        // Let's say it's 50 * Level * Level (Exponential) or just 50 * Level
        if ($autoMinerLevel == 0)
            return 0;
        return 50 * $autoMinerLevel;
    }
}