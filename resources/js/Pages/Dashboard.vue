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
import { onMounted, reactive, ref } from 'vue';


const popularStocks = ref([]);
const indices = ref([]);
const gainers = ref([]);
const losers = ref([]);
const sectors = ref([]);
const calendar = ref([]);
const aiSummary = ref(null);
const marketNews = ref([]);
const marketGraph = ref([]);

const isLoading = reactive({
    popularStocks: true,
    indices: true,
    gainers: true,
    losers: true,
    sectors: true,
    calendar: true,
    aiSummary: true,
    marketNews: true,
    marketGraph: true,
});


onMounted(async () => {
    const popularStocksResponse = await fetch(route('popular_stocks'));
    popularStocks.value = await popularStocksResponse.json();
    isLoading.popularStocks = false;

    const marketGraphResponse = await fetch(route('market_graph'));
    marketGraph.value = await marketGraphResponse.json();
    isLoading.marketGraph= false;

    const marketNewsResponse = await fetch(route('market_news'));
    marketNews.value = await marketNewsResponse.json();
    isLoading.marketNews = false;


    const topMoversResponse = await fetch(route('top_movers'));
    const topMoversData = await topMoversResponse.json();
    gainers.value = topMoversData.gainers;
    isLoading.gainers = false;
    losers.value = topMoversData.losers;
    isLoading.losers = false;

    const sectorsResponse = await fetch(route('sectors'));
    sectors.value = await sectorsResponse.json();
    isLoading.sectors = false;


    const calendarResponse = await fetch(route('economic_calendar'));
    calendar.value = await calendarResponse.json();
    isLoading.calendar = false;

    const aiSummaryResponse = await fetch(route('aianalyst-summary'));
    aiSummary.value = await aiSummaryResponse.json();
    isLoading.aiSummary = false;
    const indicesResponse = await fetch(route('indices'));
    indices.value = await indicesResponse.json();
    isLoading.indices = false;

});

</script>

<template>

    <Head title="Dashboard" />
    <AppLayout>
        <div class="overflow-hidden bg-gray-900 border-y border-gray-700 py-2 mb-6" v-if="gainers && gainers.length">
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
        <div class="overflow-hidden bg-gray-900 border-y border-gray-700 py-2 mb-6" v-if="losers && losers.length">
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
                <div v-if="isLoading.marketGraph" class="h-64 bg-gray-800 rounded animate-pulse"></div>

                <MarketGraph :marketGraph="marketGraph" class="mb-8" v-if="marketGraph" />
                <div class="mb-8" v-if="popularStocks && popularStocks.length">
                    <h2 class="text-2xl font-bold mb-4">Popular Stocks</h2>
                    <div v-if="isLoading.popularStocks" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
                        <div class="bg-gray-800 rounded p-4 h-24 animate-pulse" v-for="i in 4" :key="i"></div>
                    </div>
                    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
                        <StockCard v-for="stock in popularStocks" :key="stock.symbol" :stock="stock" />
                    </div>
                </div>
                <TopMovers :gainers="gainers" :losers="losers" class="mb-8" v-if="gainers && gainers.length" />
                <div v-if="isLoading.gainers || isLoading.losers" class="h-48 bg-gray-800 rounded animate-pulse"></div>
                <MarketIndices :indices="indices" class="mb-8" v-if="indices && indices.length" />
                <div v-if="isLoading.indices" class="h-48 bg-gray-800 rounded animate-pulse"></div>

                <SectorPerformance :sectors="sectors" class="mb-8" v-if="sectors && sectors.length" />
                <div v-if="isLoading.sectors" class="h-48 bg-gray-800 rounded animate-pulse"></div>

                <EconomicCalendar :calendar="calendar" class="mb-8" v-if="calendar" />
                <div v-if="isLoading.calendar" class="h-48 bg-gray-800 rounded animate-pulse"></div>

                <AIAnalystSummary :aiSummary="aiSummary" class="mb-8" v-if="aiSummary" />
                <div v-if="isLoading.aiSummary" class="h-48 bg-gray-800 rounded animate-pulse"></div>



                <div class="mb-8" v-if="marketNews && marketNews.length">
                    <h2 class="text-2xl font-bold mb-4">Market News</h2>
                    <div v-if="isLoading.marketNews" class="h-48 bg-gray-800 rounded animate-pulse"></div>

                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                        <NewsCard v-for="(news, index) in marketNews" :key="index" :news="news" />
                    </div>
                </div>
            </div>
        </div>
    </AppLayout>
</template>