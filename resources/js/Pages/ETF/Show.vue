<script setup>
import { Head } from '@inertiajs/vue3';
import AppLayout from '@/Layouts/AppLayout.vue';

import StockChart from '@/Components/StockChart.vue';
import StockOverview from '@/Components/StockOverview.vue';
import StockFinancials from '@/Components/StockFinancials.vue';
import StockEarnings from '@/Components/StockEarnings.vue';

import StockNews from '@/Components/StockNews.vue';
import StockPeers from '@/Components/StockPeers.vue';
import StockAnalystRecommendations from '@/Components/StockAnalystRecommendations.vue';
import ETFOverview from '@/Components/ETFOverview.vue';

const props = defineProps({
    symbol: String,
    profile: Object,      // ETF profile from FMP
    quote: Object,        // ETF quote from FMP
    financials: Object,
    earnings: Array,
    chartData: Object,
    news: Array,
    peers: Array,
    recommendations: Array,
});
</script>

<template>
  <Head :title="`${symbol} ETF Details`" />

  <AppLayout>
    <div class="py-8 max-w-7xl mx-auto sm:px-6 lg:px-8">

      <div class="mb-8 flex items-center justify-between">
        <div>
          <h1 class="text-3xl font-bold">
            {{ profile.companyName || symbol }}
          </h1>
          <p class="text-white-600">
            {{ profile.exchangeShortName || profile.exchange || 'Unknown Exchange' }}
          </p>
          <p class="mt-2 max-w-xl text-white-400">
            {{ profile.description || 'No description available.' }}
          </p>
          <p class="mt-1 text-sm text-white-500">
            Sector: {{ profile.sector || 'N/A' }}
          </p>
        </div>

        <div class="text-right">
          <div class="text-3xl font-bold">
            ${{ quote.price?.toFixed(2) || 'N/A' }}
          </div>
          <div :class="{
            'text-green-500': quote.changes >= 0,
            'text-red-500': quote.changes < 0,
          }">
            {{ quote.changes >= 0 ? '+' : '' }}{{ quote.changes?.toFixed(2) || 'N/A' }}
            ({{ quote.changesPercentage >= 0 ? '+' : '' }}{{ quote.changesPercentage?.toFixed(2) || 'N/A' }}%)
          </div>
        </div>
      </div>

      <div class="border border-gray-800 bg-default text-white shadow-sm rounded-lg shadow p-6 mb-8">
        <StockChart :symbol="symbol" :chartData="chartData" />
      </div>

      <div class="grid grid-cols-1 lg:grid-cols-3 gap-8 mb-8">
        <div class="lg:col-span-2">
          <ETFOverview :profile="profile" :quote="quote" />
        </div>
        <div>
            {{ financials }}
            {{ earnings }}
            {{ peers }}
          <StockFinancials :financials="financials" />
        </div>
      </div>

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
  </AppLayout>
</template>
