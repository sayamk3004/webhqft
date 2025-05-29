<script setup>
defineProps({
    status: Object,
});
</script>

<template>
    <div class="border border-gray-800 bg-default text-white shadow-sm rounded-lg shadow p-6">
    {{status}}
        <h2 class="text-xl font-bold mb-4">Market Status</h2>
        <div class="flex items-center">
            <div class="w-3 h-3 rounded-full mr-2" :class="{
                'bg-green-500': status?.isOpen,
                'bg-red-500': !status?.isOpen,
            }"></div>
            <span class="font-medium">{{ status?.isOpen ? 'Market is Open' : 'Market is Closed' }}</span>
            <span class="mx-2 text-gray-400">|</span>
            <span class="text-gray-600">Current Session: {{ status?.currentSession || 'N/A' }}</span>
            <span class="mx-2 text-gray-400">|</span>
            <span class="text-gray-600">Next Opening: {{ status?.nextOpening || 'N/A' }}</span>
        </div>
    </div>
</template>