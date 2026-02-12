<script setup>
import { ref, onMounted } from 'vue';
import axios from 'axios';

const players = ref([]);

const fetchLeaderboard = async () => {
    try {
        const response = await axios.get('/api/game/leaderboard');
        players.value = response.data;
    } catch (e) {
        console.error(e);
    }
};

onMounted(() => {
    fetchLeaderboard();
    // Poll every 10 seconds for updates since websockets might be tricky without setup
    setInterval(fetchLeaderboard, 10000);
});
</script>

<template>
    <div class="pb-20">
        <h2 class="text-xl font-bold mb-4 text-center text-yellow-500">Global Warlords</h2>
        
        <div class="bg-gray-800 rounded-xl overflow-hidden">
            <div v-for="(player, index) in players" :key="player.id" class="flex items-center justify-between p-4 border-b border-gray-700 last:border-0">
                <div class="flex items-center gap-4">
                    <div class="w-8 h-8 rounded-full flex items-center justify-center font-bold" 
                         :class="index === 0 ? 'bg-yellow-500 text-black' : (index === 1 ? 'bg-gray-400 text-black' : (index === 2 ? 'bg-orange-700 text-white' : 'bg-gray-700 text-gray-400'))">
                        {{ index + 1 }}
                    </div>
                    <div>
                        <p class="font-bold text-white">{{ player.name }}</p>
                        <p class="text-xs text-gray-400">Power: {{ player.power }}</p>
                    </div>
                </div>
                <div class="font-mono text-yellow-400">
                    {{ player.coins.toLocaleString() }}
                </div>
            </div>
            
            <div v-if="players.length === 0" class="p-8 text-center text-gray-500">
                Loading rankings...
            </div>
        </div>
    </div>
</template>
