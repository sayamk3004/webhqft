<script setup>
const props = defineProps({
  profile: Object,  // ETF profile from FMP
  quote: Object,    // ETF quote from FMP
});
</script>

<template>
  <div class="bg-gray-900 p-6 rounded-lg shadow-md text-white">
    <h2 class="text-2xl font-semibold mb-4">Overview</h2>

    <dl class="grid grid-cols-1 md:grid-cols-2 gap-x-6 gap-y-4">
      <div>
        <dt class="font-medium text-gray-400">Company Name</dt>
        <dd class="mt-1 text-lg">{{ profile.companyName || 'N/A' }}</dd>
      </div>

      <div>
        <dt class="font-medium text-gray-400">Symbol</dt>
        <dd class="mt-1 text-lg">{{ profile.symbol || 'N/A' }}</dd>
      </div>

      <div>
        <dt class="font-medium text-gray-400">Exchange</dt>
        <dd class="mt-1 text-lg">{{ profile.exchangeShortName || profile.exchange || 'N/A' }}</dd>
      </div>

      <div>
        <dt class="font-medium text-gray-400">Sector</dt>
        <dd class="mt-1 text-lg">{{ profile.sector || 'N/A' }}</dd>
      </div>

      <div>
        <dt class="font-medium text-gray-400">Industry</dt>
        <dd class="mt-1 text-lg">{{ profile.industry || 'N/A' }}</dd>
      </div>
      <div>
        <dt class="font-medium text-gray-400">Market Cap</dt>
        <dd class="mt-1 text-lg">
          {{ quote.marketCap ? new Intl.NumberFormat().format(quote.marketCap) : 'N/A' }}
        </dd>
      </div>

      <div>
        <dt class="font-medium text-gray-400">Average Volume</dt>
        <dd class="mt-1 text-lg">
          {{ quote.avgVolume ? new Intl.NumberFormat().format(quote.avgVolume) : 'N/A' }}
        </dd>
      </div>

      <div>
        <dt class="font-medium text-gray-400">EPS</dt>
        <dd class="mt-1 text-lg">{{ quote.eps ?? 'N/A' }}</dd>
      </div>
            <div>
        <dt class="font-medium text-gray-400">PE</dt>
        <dd class="mt-1 text-lg">{{ quote.pe ?? 'N/A' }}</dd>
      </div>

      <div class="md:col-span-2">
        <dt class="font-medium text-gray-400">Description</dt>
        <dd class="mt-1 text-base">{{ profile.description || 'No description available.' }}</dd>
      </div>
    </dl>
  </div>
</template>
