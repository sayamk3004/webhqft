<script setup>
import AppLayout from '@/Layouts/AppLayout.vue';
import { computed } from 'vue';

const props = defineProps({
    index: Object,
});

const priceChange = computed(() => {
    const change = props.index.change ?? 0;
    const percent = props.index.changesPercentage ?? 0;
    return `${change.toFixed(2)} (${percent.toFixed(2)}%)`;
});

const isPositive = computed(() => (props.index.change ?? 0) >= 0);

const format = (val) =>
    val !== undefined && val !== null
        ? typeof val === 'number'
            ? val.toLocaleString(undefined, { maximumFractionDigits: 2 })
            : val
        : '—';
</script>

<template>
    <AppLayout>
        <div class="max-w-5xl mx-auto px-6 py-12 text-white">
            <div class="bg-gray-900 rounded-2xl p-8 shadow-xl">
                <div class="flex justify-between items-start mb-4">
                    <div>
                        <h1 class="text-4xl font-extrabold tracking-tight">{{ index.name }}</h1>
                        <p class="text-sm text-gray-400 mt-1">
                            {{ index.symbol }} • {{ index.exchange || 'Unknown Exchange' }}
                        </p>
                    </div>
                    <div class="text-right">
                        <div class="text-3xl font-bold">
                            ${{ format(index.price) }}
                        </div>
                        <div
                            :class="isPositive ? 'text-green-500' : 'text-red-500'"
                            class="text-sm font-medium"
                        >
                            {{ priceChange }}
                        </div>
                    </div>
                </div>

                <!-- Price Highlights -->
                <div class="grid grid-cols-2 md:grid-cols-3 gap-6 mt-8 text-sm">
                    <div class="bg-gray-800 p-4 rounded-lg">
                        <div class="text-gray-400">Day High</div>
                        <div class="font-semibold text-green-400">${{ format(index.dayHigh) }}</div>
                    </div>
                    <div class="bg-gray-800 p-4 rounded-lg">
                        <div class="text-gray-400">Day Low</div>
                        <div class="font-semibold text-red-400">${{ format(index.dayLow) }}</div>
                    </div>
                    <div class="bg-gray-800 p-4 rounded-lg">
                        <div class="text-gray-400">Open</div>
                        <div class="font-semibold">${{ format(index.open) }}</div>
                    </div>
                    <div class="bg-gray-800 p-4 rounded-lg">
                        <div class="text-gray-400">Previous Close</div>
                        <div class="font-semibold">${{ format(index.previousClose) }}</div>
                    </div>
                    <div class="bg-gray-800 p-4 rounded-lg">
                        <div class="text-gray-400">52W High</div>
                        <div class="font-semibold text-green-400">${{ format(index.yearHigh) }}</div>
                    </div>
                    <div class="bg-gray-800 p-4 rounded-lg">
                        <div class="text-gray-400">52W Low</div>
                        <div class="font-semibold text-red-400">${{ format(index.yearLow) }}</div>
                    </div>
                </div>

                <!-- Averages & Volume -->
                <div class="grid grid-cols-2 md:grid-cols-3 gap-6 mt-8 text-sm">
                    <div class="bg-gray-800 p-4 rounded-lg">
                        <div class="text-gray-400">50-Day Avg</div>
                        <div class="font-semibold">${{ format(index.priceAvg50) }}</div>
                    </div>
                    <div class="bg-gray-800 p-4 rounded-lg">
                        <div class="text-gray-400">200-Day Avg</div>
                        <div class="font-semibold">${{ format(index.priceAvg200) }}</div>
                    </div>
                    <div class="bg-gray-800 p-4 rounded-lg">
                        <div class="text-gray-400">Market Cap</div>
                        <div class="font-semibold">
                            {{ index.marketCap ? `$${(index.marketCap / 1e9).toFixed(2)}B` : 'N/A' }}
                        </div>
                    </div>
                    <div class="bg-gray-800 p-4 rounded-lg">
                        <div class="text-gray-400">Volume</div>
                        <div class="font-semibold">{{ index.volume || 'N/A' }}</div>
                    </div>
                    <div class="bg-gray-800 p-4 rounded-lg">
                        <div class="text-gray-400">Avg Volume</div>
                        <div class="font-semibold">{{ index.avgVolume || 'N/A' }}</div>
                    </div>
                </div>
            </div>

            <!-- Chart Placeholder -->
            <div class="mt-12 bg-gray-900 p-8 rounded-2xl shadow-xl">
                <h2 class="text-xl font-semibold mb-4">📈 Trend Chart (Coming Soon)</h2>
                <div class="h-64 bg-gray-800 rounded-lg flex items-center justify-center text-gray-400 italic">
                    Historical chart not available for this index yet.
                </div>
            </div>
        </div>
    </AppLayout>
</template>

<style scoped>
/* subtle hover animations if you expand it later */
.bg-gray-800:hover {
  background-color: #374151;
}
</style>
