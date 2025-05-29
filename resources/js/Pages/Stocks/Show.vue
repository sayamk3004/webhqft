<script setup>
import { Head, Link } from '@inertiajs/vue3';
import AppLayout from '@/Layouts/AppLayout.vue';
import StockChart from '@/Components/StockChart.vue';
import StockOverview from '@/Components/StockOverview.vue';
import StockFinancials from '@/Components/StockFinancials.vue';
import StockEarnings from '@/Components/StockEarnings.vue';

import StockNews from '@/Components/StockNews.vue';
import StockPeers from '@/Components/StockPeers.vue';
import StockAnalystRecommendations from '@/Components/StockAnalystRecommendations.vue';

const props = defineProps({
    symbol: String,
    profile: Object,
    quote: Object,
    financials: Object,
    earnings: Array,
    chartData: Object,
    news: Array,
    peers: Array,
    recommendations: Array
});

</script>

<template>
    <Head :title="`${symbol} Stock`" />

    <AppLayout>
        <div class="py-8">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <!-- Stock Header -->
                <div class="mb-8">
                    <div class="flex items-center justify-between">
                        <div>
                            <h1 class="text-3xl font-bold">{{ profile.name }} ({{ symbol }})</h1>
                            <p class="text-gray-600">{{ profile.exchange }}</p>
                        </div>
                        <div class="text-right">
                            <div class="text-3xl font-bold">
                                ${{ quote.c?.toFixed(2) || 'N/A' }}
                            </div>
                            <div
                                :class="{
                                    'text-green-500': quote.d && quote.d >= 0,
                                    'text-red-500': quote.d && quote.d < 0,
                                }"
                            >
                                {{ quote.d?.toFixed(2) || 'N/A' }} ({{ quote.dp?.toFixed(2) || 'N/A' }}%)
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Stock Chart -->
                <div class="border border-gray-800 bg-default text-white shadow-sm rounded-lg shadow p-6 mb-8">
                    <StockChart :symbol="symbol" :chartData="chartData" />
                </div>
                

                <!-- Stock Overview -->
                <div class="grid grid-cols-1 lg:grid-cols-3 gap-8 mb-8">
                    <div class="lg:col-span-2">
                        <StockOverview :profile="profile" :quote="quote" />
                    </div>
                    <div>
                        <StockFinancials :financials="financials" />
                    </div>
                </div>

                <!-- Earnings -->
                <div class="border border-gray-800 bg-default text-white shadow-sm rounded-lg shadow p-6 mb-8">
                    <StockEarnings :earnings="earnings" />
                </div>
                         <div class="border border-gray-800 bg-default text-white shadow-sm rounded-lg shadow p-6 mb-8">
<StockPeers :peers="peers" />
    </div>
         <div class="border border-gray-800 bg-default text-white shadow-sm rounded-lg shadow p-6 mb-8">
<StockAnalystRecommendations :recommendations="recommendations" />
    </div>
                             <div class="border border-gray-800 bg-default text-white shadow-sm rounded-lg shadow p-6 mb-8">
                <StockNews :news="news" />
                    </div>
            </div>
        </div>
    </AppLayout>
</template>