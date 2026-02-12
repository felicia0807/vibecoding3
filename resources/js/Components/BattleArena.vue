<script setup>
import { ref, defineProps, defineEmits } from 'vue';
import axios from 'axios';

const props = defineProps(['user']);
const emit = defineEmits(['battle-complete']);

const battleLog = ref(null);
const loading = ref(false);

const attack = async () => {
    loading.value = true;
    battleLog.value = null;
    try {
        const response = await axios.post('/api/game/attack');
        battleLog.value = response.data;
        emit('battle-complete');
    } catch (e) {
        battleLog.value = {
            error: true,
            message: e.response?.data?.message || 'Attack failed'
        };
    } finally {
        loading.value = false;
    }
};
</script>

<template>
    <div class="flex flex-col items-center justify-center p-4">
        <div class="text-center mb-8">
            <h2 class="text-2xl font-bold text-red-500 mb-2">WAR ZONE</h2>
            <p class="text-gray-400">Attack random players to steal their gold!</p>
        </div>

        <div class="bg-gray-800 p-6 rounded-2xl border border-red-900 w-full max-w-sm text-center mb-8">
            <p class="text-sm text-gray-400 uppercase tracking-widest">Your Power</p>
            <p class="text-4xl font-bold text-white">{{ user?.total_attack_power || 0 }}</p>
        </div>

        <button 
            @click="attack"
            :disabled="loading"
            class="w-full max-w-sm py-4 bg-red-600 hover:bg-red-500 disabled:bg-red-900 text-white font-bold text-xl rounded-xl shadow-lg transform transition active:scale-95"
        >
            {{ loading ? 'SEARCHING TARGET...' : '⚔️ ATTACK ENEMY' }}
        </button>

        <!-- Battle Result -->
        <div v-if="battleLog" class="mt-8 w-full max-w-sm bg-gray-900 p-4 rounded-lg border" :class="battleLog.error ? 'border-yellow-500' : (battleLog.result === 'User Won' ? 'border-green-500' : 'border-red-500')">
            <div v-if="battleLog.error">
                <p class="text-yellow-500">{{ battleLog.message }}</p>
            </div>
            <div v-else>
                <h3 class="text-lg font-bold" :class="battleLog.result === 'User Won' ? 'text-green-400' : 'text-red-400'">
                    {{ battleLog.result === 'User Won' ? 'VICTORY!' : 'DEFEAT' }}
                </h3>
                <p class="text-white mt-2">Opponent: <span class="font-bold">{{ battleLog.enemy }}</span></p>
                <p v-if="battleLog.result === 'User Won'" class="text-yellow-400 mt-1 font-bold">
                    +{{ battleLog.coins_stolen }} Coins Stolen!
                </p>
                <p v-else class="text-red-400 mt-1">
                    You failed to breach their defenses.
                </p>
            </div>
        </div>
    </div>
</template>
