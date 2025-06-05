<script setup>
import { Head, Link } from '@inertiajs/vue3';
import AppLayout from '@/Layouts/AppLayout.vue';
import StockCard from '@/Components/StockCard.vue';
import NewsCard from '@/Components/NewsCard.vue';
import MarketStatus from '@/Components/MarketStatus.vue';

import MarketIndices from '@/Components/MarketIndices.vue';
import TopMovers from '@/Components/TopMovers.vue';
import SectorPerformance from '@/Components/SectorPerformance.vue';
import EconomicCalendar from '@/Components/EconomicCalendar.vue';
import AIAnalystSummary from '@/Components/AIAnalystSummary.vue';
import MarketGraph from '@/Components/MarketGraph.vue';

defineProps({
    popularStocks: Array,
    marketNews: Array,
    marketStatus: Object,
    indices: Object,
    gainers: Object,
    losers: Object,
    sectors: Array,
    calendar: Array,
    aiSummary: Object,
    marketGraph: Object
});
</script>

<template>

    <Head title="Dashboard" />
    <AppLayout>
        <div class="overflow-hidden bg-gray-900 border-y border-gray-700 py-2 mb-6">
            <div class="animate-marquee whitespace-nowrap text-sm text-white space-x-6 px-4">
                <span v-for="(stock, i) in gainers" :key="i" class="inline-block">
                    <span class="font-semibold">{{ stock.ticker }}</span>
                    <span class="mx-1">${{ stock.price }}</span>
                    <span :class="stock.changes >= 0 ? 'text-green-400' : 'text-red-400'">
                        ({{ stock.changes >= 0 ? '+' : '' }}{{ stock.changes.toFixed(2) }})
                    </span>
                </span>
            </div>
        </div>
        <div class="overflow-hidden bg-gray-900 border-y border-gray-700 py-2 mb-6">
            <div class="animate-marquee whitespace-nowrap text-sm text-white space-x-6 px-4">
                <span v-for="(stock, i) in losers" :key="i" class="inline-block">
                    <span class="font-semibold">{{ stock.ticker }}</span>
                    <span class="mx-1">${{ stock.price }}</span>
                    <span :class="stock.changes >= 0 ? 'text-green-400' : 'text-red-400'">
                        ({{ stock.changes >= 0 ? '+' : '' }}{{ stock.changes.toFixed(2) }})
                    </span>
                </span>
            </div>
        </div>

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <h1>Financial Market Insights</h1>
                <MarketGraph :marketGraph="marketGraph" class="mb-8" />
                <div class="mb-8">
                    <h2 class="text-2xl font-bold mb-4">Popular Stocks</h2>
                    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
                        <StockCard v-for="stock in popularStocks" :key="stock.symbol" :stock="stock" />
                    </div>
                </div>
                <MarketIndices :indices="indices" class="mb-8" />
                <TopMovers :gainers="gainers" :losers="losers" class="mb-8" />
                <SectorPerformance :sectors="sectors" class="mb-8" />
                <EconomicCalendar :calendar="calendar" class="mb-8" />
                <AIAnalystSummary :aiSummary="aiSummary" class="mb-8" />



                <div class="mb-8">
                    <h2 class="text-2xl font-bold mb-4">Market News</h2>
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                        <NewsCard v-for="(news, index) in marketNews" :key="index" :news="news" />
                    </div>
                </div>
            </div>
        </div>
    </AppLayout>
</template>