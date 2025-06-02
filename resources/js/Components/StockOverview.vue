<script setup>
defineProps({
    profile: Object,
    quote: Object,
});
</script>

<template>
    <div class="border border-gray-800 bg-default text-white shadow-sm rounded-lg shadow p-6">
        <h2 class="text-xl font-bold mb-4">Overview</h2>
        
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
                <h3 class="font-semibold text-white-700">Company Information</h3>
                <p class="mt-2">{{ profile.name }}</p>
                <p class="text-sm text-white-600">{{ profile.finnhubIndustry }}</p>
                <p class="text-sm text-white-600 mt-2">{{ profile.weburl }}</p>
            </div>
            
            <div>
                <h3 class="font-semibold text-white-700">Market Data</h3>
                <div class="mt-2 space-y-2">
                    <div class="flex justify-between">
                        <span class="text-white-600">Open</span>
                        <span>${{ quote.o?.toFixed(2) || 'N/A' }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-white-600">High</span>
                        <span>${{ quote.h?.toFixed(2) || 'N/A' }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-white-600">Low</span>
                        <span>${{ quote.l?.toFixed(2) || 'N/A' }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-white-600">Previous Close</span>
                        <span>${{ quote.pc?.toFixed(2) || 'N/A' }}</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>