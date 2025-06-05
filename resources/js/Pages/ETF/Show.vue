<template>

  <Head :title="`${symbol} ETF Details`" />
  <AppLayout>
    <div class="py-8 max-w-7xl mx-auto sm:px-6 lg:px-8 text-white">
      <div class="overflow-hidden bg-gray-900 border-y border-gray-700 py-2 mb-6">
        <div class="animate-marquee whitespace-nowrap text-sm text-white space-x-6 px-4">
          <span v-for="(etf, i) in topMovers" :key="i" class="inline-block">
            <span class="font-semibold">{{ etf.symbol }}</span>
            <span class="mx-1">${{ etf.price.toFixed(2) }}</span>
            <span :class="etf.change >= 0 ? 'text-green-400' : 'text-red-400'">
              ({{ etf.change >= 0 ? '+' : '' }}{{ etf.change.toFixed(2) }})
            </span>
          </span>
        </div>
      </div>
      <div class="mb-6 px-6 py-5 rounded-xl bg-gray-900 border border-gray-700 shadow-sm">
        <div class="flex flex-col lg:flex-row justify-between items-start lg:items-center gap-6">
          <div>
            <h1 class="text-3xl font-semibold text-green-400">
              {{ profile.companyName || symbol }}
            </h1>
            <p class="text-sm text-gray-400">
              {{ profile.exchangeShortName || profile.exchange || 'Unknown Exchange' }} |
              Sector:
              <span class="text-green-500 font-medium">{{ profile.sector || 'N/A' }}</span>
            </p>
            <p class="mt-2 text-sm text-gray-300 max-w-2xl leading-relaxed">
              {{ profile.description || 'No description available.' }}
            </p>
          </div>
          <div class="text-right">
            <div class="text-4xl font-bold text-green-300">
              ${{ quote.price?.toFixed(2) || 'N/A' }}
            </div>
            <div class="mt-1 text-sm font-semibold" :class="{
              'text-green-500': quote.change >= 0,
              'text-red-500': quote.change < 0,
            }">
              {{ quote.change >= 0 ? '+' : '' }}{{ quote.change?.toFixed(2) || 'N/A' }}
              ({{ quote.changesPercentage >= 0 ? '+' : '' }}{{ quote.changesPercentage?.toFixed(2) || 'N/A' }}%)
            </div>
            <AddToWatchlistButton :symbol="symbol" type="etf" class="my-2" />

          </div>

        </div>
      </div>

      <div class="border border-gray-800 bg-default shadow-sm rounded-lg p-6 mb-8">
        <StockChart :symbol="symbol" :chartData="chartData" />
      </div>
      <div class="bg-gray-900 rounded-lg shadow-lg">
        <div class="border-b border-gray-700 px-4 pt-4">
          <nav class="flex space-x-6">
            <button v-for="tab in tabs" :key="tab" @click="activeTab = tab" :class="[
              'px-4 py-2 font-medium',
              activeTab === tab
                ? 'text-white border-b-2 border-indigo-500'
                : 'text-gray-400 hover:text-white'
            ]">
              {{ tab }}
            </button>
          </nav>
        </div>
        <div class="p-6">
          <div v-if="activeTab === 'Overview'">
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-8 mb-8">
              <div class="lg:col-span-2">
                <ETFOverview :profile="profile" :quote="quote" />
              </div>
              <div>
                <ETFFinancials :financials="financials" v-if="financials && financials.length" />
              </div>
            </div>
          </div>

          <div v-if="activeTab === 'Dividends'">
            <DistributionsTable :distributions="distributions" v-if="distributions && distributions.length" />
          </div>
          <div v-if="activeTab === 'Holdings'">
            <ETFHoldings :holdings="holdings" v-if="holdings && holdings.length" />
          </div>

          <div v-if="activeTab === 'Performance'">
            <ETFPerformance :performance="performance" v-if="performance" />
          </div>

          <div v-if="activeTab === 'Peers'">
            <ETFPeers :peers="peers" v-if="peers && peers.length" />
          </div>

          <div v-if="activeTab === 'News'">
            <StockNews :news="news" v-if="news && news.length" />
          </div>
        </div>
      </div>
    </div>
  </AppLayout>
</template>

<script setup>
import { ref, watch } from 'vue';
import { Head } from '@inertiajs/vue3';
import AppLayout from '@/Layouts/AppLayout.vue';

import StockChart from '@/Components/StockChart.vue';
import StockNews from '@/Components/StockNews.vue';

import ETFOverview from '@/Components/ETFOverview.vue';
import ETFPeers from '@/Components/ETFPeers.vue';
import ETFFinancials from '@/Components/ETFFinancials.vue';
import DistributionsTable from '@/Components/DistributionsTable.vue';
import ETFHoldings from '@/Components/ETFHoldings.vue';
import ETFPerformance from '@/Components/ETFPerformance.vue';
import AddToWatchlistButton from '@/Components/AddToWatchlistButton.vue';

const props = defineProps({
  symbol: String,
  profile: Object,
  quote: Object,
  // financials: Object,
  // distributions: Array,
  chartData: Object,
  // news: Array,
  // peers: Array,
  // holdings: Array,
  // performance: Array,
  topMovers: Array
});

const financials = ref(null);
const distributions = ref(null);
const peers = ref(null);
const holdings = ref(null);
const performance = ref(null);
const news = ref(null);

const tabs = ['Overview', 'Dividends', 'Holdings', 'Performance', 'Peers', 'News'];
const activeTab = ref('');

activeTab.value = 'Overview';

const loadedTabs = ref([]);

watch(activeTab, async (newTab) => {

  const { data } = await axios.get(route('etfs.loadTabData', {
    symbol: props.symbol,
    tab: newTab.toLowerCase()
  }), {
    timeout: 30000 // 10 seconds
  });
  console.log(data);
  switch (newTab) {
    case 'Dividends':
      distributions.value = data.distributions;
      break;
    case 'Holdings':
      holdings.value = data.holdings;
      break;
    case 'Peers':
      peers.value = data.peers;
      break;
    case 'Performance':
      performance.value = data.performance;
      break;
    case 'News':
      news.value = data.news;
      break;
    case 'Overview':
      financials.value = data.financials;
      break;
  }

  loadedTabs.value.push(newTab);
});
</script>
