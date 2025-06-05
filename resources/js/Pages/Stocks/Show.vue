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
import { ref } from 'vue';
import axios from 'axios';
import AddToWatchlistButton from '@/Components/AddToWatchlistButton.vue';
import CompareButton from '@/Components/CompareButton.vue';

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

const tabs = [
  'Overview',
  'Earnings',
  'Peers',
  'Analyst Ratings',
  'News'
];

const activeTab = ref('Overview');

</script>

<template>
  <AppLayout>
    <Head :title="`${symbol} Stock`" />

    <div class="py-8 max-w-7xl mx-auto sm:px-6 lg:px-8">
      <div class="mb-8 flex justify-between items-center">
        <div>
          <h1 class="text-3xl font-bold">{{ profile.name }} ({{ symbol }})</h1>
          <p class="text-white-600">{{ profile.exchange }}</p>
        </div>
        <div class="text-right">
          <div class="text-3xl font-bold">
            ${{ quote.c?.toFixed(2) || 'N/A' }}
          </div>
          <div :class="{
            'text-green-500': quote.d && quote.d >= 0,
            'text-red-500': quote.d && quote.d < 0,
          }">
            {{ quote.d?.toFixed(2) || 'N/A' }} ({{ quote.dp?.toFixed(2) || 'N/A' }}%)
          </div>
            <AddToWatchlistButton :symbol="symbol" type="stock" />
                  <CompareButton :symbol="symbol" />


        </div>
      </div>

      <!-- Tabs -->
      <div class="mb-8">
        <div class="flex space-x-4 border-b border-gray-700">
          <button
            v-for="tab in tabs"
            :key="tab"
            @click="activeTab = tab"
            class="pb-2 px-4 text-sm font-semibold"
            :class="{
              'border-b-2 border-green-400 text-green-400': activeTab === tab,
              'text-gray-400 hover:text-white': activeTab !== tab
            }"
          >
            {{ tab }}
          </button>
        </div>
      </div>

      <!-- Overview + Chart -->
      <div v-if="activeTab === 'Overview'" class="border border-gray-800 bg-default text-white shadow-sm rounded-lg shadow p-6 mb-8">
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8 mb-8">
          <div class="lg:col-span-2">
            <StockOverview :profile="profile" :quote="quote" />
          </div>
          <div>
            <StockFinancials :financials="financials" />
          </div>
        </div>
        <div>
          <StockChart :symbol="symbol" :chartData="chartData" />
        </div>
      </div>

      <!-- Earnings -->
      <div v-if="activeTab === 'Earnings'" class="border border-gray-800 bg-default text-white shadow-sm rounded-lg shadow p-6 mb-8">
        <StockEarnings :earnings="earnings" />
      </div>

      <!-- Peers -->
      <div v-if="activeTab === 'Peers'" class="border border-gray-800 bg-default text-white shadow-sm rounded-lg shadow p-6 mb-8">
        <StockPeers :peers="peers" />
      </div>

      <!-- Analyst Ratings -->
      <div v-if="activeTab === 'Analyst Ratings'" class="border border-gray-800 bg-default text-white shadow-sm rounded-lg shadow p-6 mb-8">
        <StockAnalystRecommendations :recommendations="recommendations" />
      </div>

      <!-- News -->
      <div v-if="activeTab === 'News'" class="border border-gray-800 bg-default text-white shadow-sm rounded-lg shadow p-6 mb-8">
        <StockNews :news="news" />
      </div>
    </div>
  </AppLayout>
</template>
