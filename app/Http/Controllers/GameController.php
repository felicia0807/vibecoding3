<?php

namespace App\Http\Controllers;

use App\Models\Battle;
use App\Models\User;
use App\Models\UserUpgrade;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class GameController extends Controller
{
    public function initialData()
    {
        $user = Auth::user();
        $user->load('upgrades');

        return response()->json([
            'user' => $user->append(['click_power', 'auto_miner_production', 'total_attack_power', 'total_defense']),
            'upgrades' => $this->getAvailableUpgrades($user),
        ]);
    }

    public function click(Request $request)
    {
        $user = Auth::user();
        $gain = $user->click_power;

        $user->increment('coins', $gain);

        // In a real app, you might want to throttle this or verify signitures to prevent botting.
        // For this demo, we trust the client (mostly).

        return response()->json(['coins' => $user->coins, 'gained' => $gain]);
    }

    public function buyUpgrade(Request $request)
    {
        $request->validate([
            'type' => 'required|in:miner,sword,wall,shield,auto_miner',
        ]);

        $user = Auth::user();
        $type = $request->type;

        // Find or create the upgrade record to get current level
        $upgrade = $user->upgrades()->firstOrCreate(
        ['upgrade_type' => $type],
        ['level' => 0]
        );

        // Calculate cost (same logic as Model accessor, implemented here to ensure sync)
        $cost = $upgrade->cost; // Using the accessor from UserUpgrade model

        if ($user->coins < $cost) {
            return response()->json(['message' => 'Not enough coins'], 400);
        }

        DB::transaction(function () use ($user, $upgrade, $cost) {
            $user->decrement('coins', $cost);
            $upgrade->increment('level');

            // Update user stats if necessary (denormalization)
            // For now, we rely on dynamic calculations, but we could update 'power'/'defense' columns here.
            if ($upgrade->upgrade_type === 'sword') {
                $user->increment('power', 1); // Simple +1 per level for stored field
            }
            elseif ($upgrade->upgrade_type === 'wall') {
                $user->increment('defense', 5);
            }
        });

        return response()->json([
            'message' => 'Upgrade successful',
            'user' => $user->fresh()->append(['click_power', 'auto_miner_production']),
            'upgrade' => $upgrade
        ]);
    }

    public function attack(Request $request)
    {
        $attacker = Auth::user();

        // Find a random opponent that is NOT the attacker
        // In a real game, matchmaking would be more complex (similar ELO/Power).
        // Here, we pick a random user.
        $defender = User::where('id', '!=', $attacker->id)
            ->inRandomOrder()
            ->first();

        if (!$defender) {
            return response()->json(['message' => 'No opponents found'], 404);
        }

        $attackerPower = $attacker->total_attack_power;
        $defenderDefense = $defender->total_defense;

        // Check Shield
        // logic: if shield blocks, return 'blocked'
        // For simplicity, we compare raw stats.

        $win = $attackerPower > $defenderDefense;
        $stolen = 0;

        if ($win) {
            // Steal 5-20% of coins
            $percent = rand(5, 20) / 100;
            $stolen = (int)($defender->coins * $percent);

            DB::transaction(function () use ($attacker, $defender, $stolen) {
                $defender->decrement('coins', $stolen);
                $attacker->increment('coins', $stolen);
            });
        }
        else {
            // Lose coins? "If you lose: You lose coins."
            $lost = (int)($attacker->coins * 0.05); // Lose 5% on fail
            $attacker->decrement('coins', $lost);
        }

        $battle = Battle::create([
            'attacker_id' => $attacker->id,
            'defender_id' => $defender->id,
            'winner_id' => $win ? $attacker->id : $defender->id,
            'coins_stolen' => $stolen,
        ]);

        // Broadcast Event here (Stubbed)
        // event(new BattleOccurred($battle));

        return response()->json([
            'result' => $win ? 'User Won' : 'User Lost',
            'coins_stolen' => $stolen,
            'enemy' => $defender->name,
            'battle_log' => $battle
        ]);
    }

    public function leaderboard()
    {
        $topPlayers = User::orderByDesc('coins')
            ->take(10)
            ->get(['id', 'name', 'coins', 'power']);

        return response()->json($topPlayers);
    }

    private function getAvailableUpgrades($user)
    {
        $types = ['miner', 'sword', 'wall', 'shield', 'auto_miner'];
        $data = [];

        foreach ($types as $type) {
            $upgrade = $user->upgrades->where('upgrade_type', $type)->first();
            $level = $upgrade ? $upgrade->level : 0;

            // Create a temporary instance to calculate next cost
            $temp = new UserUpgrade(['upgrade_type' => $type, 'level' => $level]);

            $data[] = [
                'type' => $type,
                'level' => $level,
                'cost' => $temp->cost,
                // Add descriptions/effects here
            ];
        }

        return $data;
    }
}