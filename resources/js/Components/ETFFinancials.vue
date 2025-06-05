<script setup>
defineProps({
    financials: Object,
});
</script>
<template>
  <div class="border border-gray-800 bg-default text-white shadow-sm rounded-lg shadow p-6">
    <h2 class="text-xl font-bold mb-4">Key Metrics</h2>

    <div class="space-y-4">
      <div>
        <h3 class="font-semibold text-gray-700">Valuation</h3>
        <div class="mt-2 space-y-2">
          <div class="flex justify-between">
            <span class="text-gray-600">Market Cap</span>
            <span>{{ financials.marketCapitalization ? `$${(financials.marketCapitalization / 1e9).toFixed(2)}B` : 'N/A' }}</span>
          </div>
          <div class="flex justify-between">
            <span class="text-gray-600">P/E Ratio</span>
            <span>{{ financials.peRatio?.toFixed(2) || 'N/A' }}</span>
          </div>
          <div class="flex justify-between">
            <span class="text-gray-600">EPS</span>
            <span>{{ financials.eps?.toFixed(2) || 'N/A' }}</span>
          </div>
        </div>
      </div>

      <div>
        <h3 class="font-semibold text-gray-700">Price & Volume</h3>
        <div class="mt-2 space-y-2">
          <div class="flex justify-between">
            <span class="text-gray-600">50-Day Avg</span>
            <span>{{ financials.priceAvg50?.toFixed(2) || 'N/A' }}</span>
          </div>
          <div class="flex justify-between">
            <span class="text-gray-600">200-Day Avg</span>
            <span>{{ financials.priceAvg200?.toFixed(2) || 'N/A' }}</span>
          </div>
          <div class="flex justify-between">
            <span class="text-gray-600">Year High</span>
            <span>{{ financials.yearHigh?.toFixed(2) || 'N/A' }}</span>
          </div>
          <div class="flex justify-between">
            <span class="text-gray-600">Year Low</span>
            <span>{{ financials.yearLow?.toFixed(2) || 'N/A' }}</span>
          </div>
          <div class="flex justify-between">
            <span class="text-gray-600">Volume</span>
            <span>{{ financials.volume?.toLocaleString() || 'N/A' }}</span>
          </div>
          <div class="flex justify-between">
            <span class="text-gray-600">Avg Volume</span>
            <span>{{ financials.avgVolume?.toLocaleString() || 'N/A' }}</span>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>
