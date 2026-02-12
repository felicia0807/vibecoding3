<script setup>
import { ref, onMounted } from 'vue';
import ClickZone from '@/Components/ClickZone.vue';
import UpgradesShop from '@/Components/UpgradesShop.vue';
import BattleArena from '@/Components/BattleArena.vue';
import Leaderboard from '@/Components/Leaderboard.vue';
import axios from 'axios';

const user = ref(null);
const upgrades = ref([]);
const activeTab = ref('mine'); // mine, shop, battle, rank

const fetchInitialData = async () => {
    try {
        const response = await axios.get('/api/game/init');
        user.value = response.data.user;
        upgrades.value = response.data.upgrades;
    } catch (error) {
        console.error('Failed to load game data', error);
    }
};

const handleCoinUpdate = (newCoins) => {
    if (user.value) user.value.coins = newCoins;
};

const handleUpgradePurchased = (data) => {
    user.value = data.user;
    // Update the specific upgrade in the list
    const index = upgrades.value.findIndex(u => u.type === data.upgrade.upgrade_type);
    if (index !== -1) {
        upgrades.value[index].level = data.upgrade.level;
         // Recalculate cost approx or refetch
         // For simplicity, we might want to refetch or duplicate logic
         // Let's refetch to be safe for now
         fetchInitialData(); 
    }
};

onMounted(() => {
    fetchInitialData();
    
    // Stub for Echo listener
    // window.Echo.private(`user.${user.value.id}`).listen(...)
});
</script>

<template>
    <div class="min-h-screen bg-gray-900 text-white font-sans">
        <!-- Header -->
        <div class="p-4 bg-gray-800 flex justify-between items-center shadow-lg">
            <h1 class="text-2xl font-bold text-yellow-400">Clicker War</h1>
            <div v-if="user" class="text-right">
                <p class="text-lg">💰 {{ user.coins.toLocaleString() }}</p>
                <p class="text-xs text-gray-400">Power: {{ user.total_attack_power }} | Def: {{ user.total_defense }}</p>
            </div>
        </div>

        <!-- Main Content -->
        <main class="p-4 safe-area-bottom">
            <div v-if="activeTab === 'mine'">
                <ClickZone :user="user" @coin-earned="handleCoinUpdate" />
            </div>
            
            <div v-if="activeTab === 'shop'">
                <UpgradesShop :upgrades="upgrades" :userCoins="user?.coins || 0" @upgrade-purchased="handleUpgradePurchased" />
            </div>

            <div v-if="activeTab === 'battle'">
                <BattleArena :user="user" @battle-complete="fetchInitialData" />
            </div>

            <div v-if="activeTab === 'rank'">
                <Leaderboard />
            </div>
        </main>

        <!-- Bottom Navigation -->
        <div class="fixed bottom-0 w-full bg-gray-800 border-t border-gray-700 flex justify-around p-4 safe-area-pb">
            <button @click="activeTab = 'mine'" :class="{'text-yellow-400': activeTab === 'mine'}" class="flex flex-col items-center">
                <span>⛏️</span>
                <span class="text-xs">Mine</span>
            </button>
            <button @click="activeTab = 'shop'" :class="{'text-yellow-400': activeTab === 'shop'}" class="flex flex-col items-center">
                <span>🛒</span>
                <span class="text-xs">Shop</span>
            </button>
            <button @click="activeTab = 'battle'" :class="{'text-yellow-400': activeTab === 'battle'}" class="flex flex-col items-center">
                <span>⚔️</span>
                <span class="text-xs">Battle</span>
            </button>
             <button @click="activeTab = 'rank'" :class="{'text-yellow-400': activeTab === 'rank'}" class="flex flex-col items-center">
                <span>🏆</span>
                <span class="text-xs">Warlords</span>
            </button>
        </div>
    </div>
</template>

<style>
.safe-area-pb {
    padding-bottom: env(safe-area-inset-bottom);
}
.safe-area-bottom {
    padding-bottom: 80px;
}
</style>
