<script setup>
import { defineProps, defineEmits } from 'vue';
import axios from 'axios';

const props = defineProps(['upgrades', 'userCoins']);
const emit = defineEmits(['upgrade-purchased']);

const buy = async (type) => {
    try {
        const response = await axios.post('/api/game/upgrade', { type });
        emit('upgrade-purchased', response.data);
    } catch (e) {
        alert(e.response?.data?.message || 'Failed to buy');
    }
};

const getIcon = (type) => {
    const icons = {
        miner: '⛏️',
        sword: '⚔️',
        wall: '🧱',
        shield: '🛡️',
        auto_miner: '🤖'
    };
    return icons[type] || '📦';
};

const getLabel = (type) => {
    return type.replace('_', ' ').toUpperCase();
};
</script>

<template>
    <div class="grid gap-4 pb-20">
        <h2 class="text-xl font-bold mb-2">Black Market</h2>
        
        <div v-for="upgrade in upgrades" :key="upgrade.type" class="bg-gray-800 p-4 rounded-xl border border-gray-700 flex justify-between items-center">
            <div class="flex items-center gap-3">
                <div class="text-3xl">{{ getIcon(upgrade.type) }}</div>
                <div>
                    <h3 class="font-bold">{{ getLabel(upgrade.type) }}</h3>
                    <p class="text-xs text-blue-400">Lvl {{ upgrade.level }}</p>
                </div>
            </div>
            
            <button 
                @click="buy(upgrade.type)"
                :disabled="userCoins < upgrade.cost"
                class="px-4 py-2 rounded-lg font-bold text-sm transition-colors"
                :class="userCoins >= upgrade.cost ? 'bg-green-600 hover:bg-green-500 text-white' : 'bg-gray-600 text-gray-400 cursor-not-allowed'"
            >
                💰 {{ upgrade.cost.toLocaleString() }}
            </button>
        </div>
    </div>
</template>
