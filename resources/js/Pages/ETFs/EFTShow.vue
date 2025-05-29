<template>
  <div v-if="loading" class="container mx-auto px-4 py-8 text-center">
    <div class="inline-block animate-spin rounded-full h-12 w-12 border-t-2 border-b-2 border-blue-500"></div>
  </div>

  <div v-else class="container mx-auto px-4 py-8">
    <div class="flex justify-between items-start mb-8">
      <div>
        <h1 class="text-3xl font-bold text-gray-800">{{ etf.symbol }}</h1>
        <h2 class="text-xl text-gray-600">{{ etf.name }}</h2>
        <div class="mt-2 flex items-center space-x-4">
          <span class="text-sm bg-blue-100 text-blue-800 px-2 py-1 rounded">
            {{ etf.assetsUnderManagement ? formatCurrency(etf.assetsUnderManagement, 0) : 'N/A' }} AUM
          </span>
          <span class="text-sm bg-gray-100 text-gray-800 px-2 py-1 rounded">
            Inception: {{ etf.inceptionDate || 'N/A' }}
          </span>
        </div>
      </div>
      <div class="text-right">
        <div class="text-3xl font-bold" :class="priceChangeColor">
          {{ formatCurrency(etf.price) }}
        </div>
        <div class="text-lg" :class="priceChangeColor">
          {{ formatChange(etf.change) }} ({{ formatPercent(etf.percentChange) }})
        </div>
      </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-8">
      <!-- Price Chart -->
      <div class="lg:col-span-2 border border-gray-800 bg-default text-white shadow-sm rounded-xl shadow-md p-6">
        <h3 class="text-lg font-semibold mb-4 text-gray-800">Performance Chart</h3>
        <div class="h-64">
          <ETFChart :data="chartData" />
        </div>
      </div>

      <!-- Key Metrics -->
      <div class="border border-gray-800 bg-default text-white shadow-sm rounded-xl shadow-md p-6">
        <h3 class="text-lg font-semibold mb-4 text-gray-800">Key Metrics</h3>
        <div class="space-y-4">
          <div v-for="(value, key) in keyMetrics" :key="key" class="flex justify-between">
            <span class="text-gray-600">{{ key }}</span>
            <span class="font-medium">{{ value }}</span>
          </div>
        </div>
      </div>
    </div>

    <!-- Holdings Section -->
    <div class="border border-gray-800 bg-default text-white shadow-sm rounded-xl shadow-md p-6 mb-8">
      <div class="flex justify-between items-center mb-4">
        <h3 class="text-lg font-semibold text-gray-800">Top Holdings</h3>
        <span class="text-sm text-gray-500">
          {{ etf.holdings.length }} holdings ({{ etf.holdings.slice(0, 10).reduce((sum, h) => sum + h.weight, 0).toFixed(1) }}% of portfolio)
        </span>
      </div>
      
      <div class="overflow-x-auto">
        <table class="min-w-full divide-y divide-gray-200">
          <thead class="bg-gray-50">
            <tr>
              <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Symbol</th>
              <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Name</th>
              <th scope="col" class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Weight</th>
              <th scope="col" class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Sector</th>
            </tr>
          </thead>
          <tbody class="border border-gray-800 bg-default text-white shadow-sm divide-y divide-gray-200">
            <tr v-for="holding in etf.holdings.slice(0, 10)" :key="holding.symbol">
              <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-blue-600">{{ holding.symbol }}</td>
              <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ holding.name }}</td>
              <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 text-right">{{ formatPercent(holding.weight / 100) }}</td>
              <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 text-right">{{ holding.sector || 'N/A' }}</td>
            </tr>
          </tbody>
        </table>
      </div>
      
      <button
        v-if="etf.holdings.length > 10"
        @click="showAllHoldings = !showAllHoldings"
        class="mt-4 text-sm text-blue-600 hover:text-blue-800 focus:outline-none"
      >
        {{ showAllHoldings ? 'Show Less' : `Show All ${etf.holdings.length} Holdings` }}
      </button>
    </div>

    <!-- Sector Allocation -->
    <div class="border border-gray-800 bg-default text-white shadow-sm rounded-xl shadow-md p-6 mb-8">
      <h3 class="text-lg font-semibold mb-4 text-gray-800">Sector Allocation</h3>
      <div class="h-64">
        <SectorChart :holdings="etf.holdings" />
      </div>
    </div>

    <!-- Profile Details -->
    <div class="border border-gray-800 bg-default text-white shadow-sm rounded-xl shadow-md p-6">
      <h3 class="text-lg font-semibold mb-4 text-gray-800">ETF Profile</h3>
      <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        <div>
          <h4 class="font-medium text-gray-700 mb-2">Description</h4>
          <p class="text-gray-600">{{ etf.description || 'No description available.' }}</p>
        </div>
        <div>
          <h4 class="font-medium text-gray-700 mb-2">Fund Details</h4>
          <div class="space-y-2">
            <div v-for="(value, key) in profileDetails" :key="key" class="flex justify-between">
              <span class="text-gray-600">{{ key }}</span>
              <span class="font-medium">{{ value }}</span>
            </div>
          </div>
        </div>
      </div>
    </div>

    <div class="mt-6 text-xs text-gray-400 text-right">
      Last updated: {{ lastUpdated }}
    </div>
  </div>
</template>

<script>
import { ref, computed, onMounted } from 'vue'
import { useRoute } from 'vue-router'
import { formatCurrency, formatPercent } from '@/utils/formatters'
import ETFChart from '@/components/ETFChart.vue'
import SectorChart from '@/components/SectorChart.vue'

export default {
  components: {
    ETFChart,
    SectorChart
  },
  setup() {
    const route = useRoute()
    const loading = ref(true)
    const etf = ref({})
    const lastUpdated = ref('')
    const showAllHoldings = ref(false)

    const priceChangeColor = computed(() => {
      if (!etf.value.percentChange) return 'text-gray-800'
      return etf.value.percentChange > 0 ? 'text-green-600' : 'text-red-600'
    })

    const keyMetrics = computed(() => ({
      'Expense Ratio': etf.value.expenseRatio ? formatPercent(etf.value.expenseRatio / 100) : 'N/A',
      'PE Ratio': etf.value.peRatio ? etf.value.peRatio.toFixed(2) : 'N/A',
      '52-Week High': etf.value.week52High ? formatCurrency(etf.value.week52High) : 'N/A',
      '52-Week Low': etf.value.week52Low ? formatCurrency(etf.value.week52Low) : 'N/A',
      'Dividend Yield': etf.value.dividendYield ? formatPercent(etf.value.dividendYield / 100) : 'N/A',
      'Average Volume': etf.value.averageVolume ? `${(etf.value.averageVolume / 1000).toFixed(1)}K` : 'N/A'
    }))

    const profileDetails = computed(() => ({
      'Issuer': etf.value.issuer || 'N/A',
      'Structure': etf.value.structure || 'N/A',
      'Domicile': etf.value.domicile || 'N/A',
      'Index Tracked': etf.value.index || 'N/A',
      'Asset Class': etf.value.assetClass || 'N/A',
      'Region': etf.value.region || 'N/A'
    }))

    const chartData = computed(() => ({
      labels: etf.value.chartData?.t?.map(t => new Date(t * 1000).toLocaleDateString()) || [],
      prices: etf.value.chartData?.c || []
    }))

    const formatChange = (value) => {
      if (value > 0) return `+${formatCurrency(value)}`
      if (value < 0) return formatCurrency(value)
      return '0.00'
    }

    const loadETFData = async () => {
      loading.value = true
      try {
        const response = await fetch(`/api/etfs/${route.params.symbol}`)
        const data = await response.json()
        etf.value = data.etf
        lastUpdated.value = data.lastUpdated
      } finally {
        loading.value = false
      }
    }

    onMounted(() => {
      loadETFData()
    })

    return {
      loading,
      etf,
      lastUpdated,
      showAllHoldings,
      priceChangeColor,
      keyMetrics,
      profileDetails,
      chartData,
      formatCurrency,
      formatPercent,
      formatChange
    }
  }
}
</script>