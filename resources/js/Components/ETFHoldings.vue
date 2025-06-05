<template>
  <div class="bg-grey shadow rounded-2xl p-6 mt-6">
    <h2 class="text-xl font-bold mb-4">Top Holdings</h2>
    <div v-if="holdings.length">
      <table class="w-full text-sm text-left border-collapse">
        <thead>
          <tr class="border-b bg-gray-100">
            <th class="py-2 px-3">Ticker</th>
            <th class="py-2 px-3">Company Name</th>
            <th class="py-2 px-3">Shares</th>
            <th class="py-2 px-3">Market Value</th>
            <th class="py-2 px-3">Weight (%)</th>
          </tr>
        </thead>
        <tbody>
          <tr v-for="(item, index) in holdings" :key="index" class="border-b hover:bg-gray-50 hover:text-black">
            <td class="py-2 px-3 font-medium">{{ item.asset || '—' }}</td>
            <td class="py-2 px-3">{{ item.name || '—' }}</td>
            <td class="py-2 px-3">{{ formatNumber(item.sharesNumber) }}</td>
            <td class="py-2 px-3">{{ formatCurrency(item.marketValue) }}</td>
            <td class="py-2 px-3">{{ item.weightPercentage?.toFixed(2) ?? '—' }}</td>
          </tr>
        </tbody>
      </table>
    </div>
    <p v-else class="text-gray-500">No holdings data available.</p>
  </div>
</template>

<script setup>
defineProps({
  holdings: {
    type: Array,
    required: true,
  },
});

function formatNumber(value) {
  return value?.toLocaleString() ?? '—';
}

function formatCurrency(value) {
  return value
    ? new Intl.NumberFormat('en-US', { style: 'currency', currency: 'USD' }).format(value)
    : '—';
}
</script>
