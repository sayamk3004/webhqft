<script setup>
defineProps({
    earnings: Array,
});
</script>

<template>
    <div>
        <h2 class="text-xl font-bold mb-4">Earnings History</h2>
        
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Date</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">EPS Estimate</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">EPS Actual</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Surprise</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Surprise Percent</th>
                    </tr>
                </thead>
                <tbody class="border border-gray-800 bg-default text-white shadow-sm divide-y divide-gray-200">
                    <tr v-for="(earning, index) in earnings" :key="index">
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ earning.period }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ earning.estimate?.toFixed(2) || 'N/A' }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium" :class="{
                            'text-green-600': earning.actual >= earning.estimate,
                            'text-red-600': earning.actual < earning.estimate,
                        }">
                            {{ earning.actual?.toFixed(2) || 'N/A' }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ earning.surprise }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium" :class="{
                            'text-green-600': earning.surprise >= earning.surprisePercent,
                            'text-red-600': earning.surprisePercent < earning.surprise,
                        }">
                           {{ earning.surprisePercent }}
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</template>