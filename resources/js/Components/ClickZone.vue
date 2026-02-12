<script setup>
import { defineProps, defineEmits, ref } from 'vue';
import axios from 'axios';

const props = defineProps(['user']);
const emit = defineEmits(['coin-earned']);

const isAnimating = ref(false);

const handleClick = async () => {
    // Optimistic UI update
    if (props.user) {
        // We guess the gain based on known power, simplified
        const gain = props.user.click_power || 1; 
        emit('coin-earned', props.user.coins + gain);
    }
    
    // Animation trigger
    isAnimating.value = true;
    setTimeout(() => isAnimating.value = false, 100);

    try {
        const response = await axios.post('/api/game/click');
        emit('coin-earned', response.data.coins);
    } catch (e) {
        console.error(e);
    }
};
</script>

<template>
    <div class="flex flex-col items-center justify-center h-[60vh]">
        <div class="relative">
            <button 
                @click="handleClick"
                class="w-64 h-64 rounded-full bg-gradient-to-b from-yellow-400 to-orange-500 shadow-[0_0_50px_rgba(255,165,0,0.5)] border-4 border-yellow-200 active:scale-95 transition-transform duration-100 flex items-center justify-center"
                :class="{'scale-95': isAnimating}"
            >
                <div class="text-center">
                    <span class="text-6xl">⛏️</span>
                    <p class="font-bold text-white mt-2 drop-shadow-md">TAP!</p>
                </div>
            </button>
            
            <!-- Floating particles could go here -->
        </div>
        
        <div class="mt-8 text-center text-gray-300">
            <p>Click Power: {{ user?.click_power || 1 }}</p>
            <p>Auto Miner: {{ user?.auto_miner_production || 0 }}/min</p>
        </div>
    </div>
</template>
