<template>
  <section class="sector-performance p-4 border border-gray-800 bg-default text-white shadow-sm rounded shadow mt-8">
    <h2 class="text-xl font-semibold mb-4">Sector Performance</h2>
    <div class="overflow-x-auto">
      <table class="w-full text-left border-collapse border border-gray-200">
        <thead>
          <tr class="bg-gray-100">
            <th class="px-4 py-2 border border-gray-300">Sector</th>
            <th class="px-4 py-2 border border-gray-300">Change (%)</th>
          </tr>
        </thead>
        <tbody>
          <tr v-for="sector in sectors" :key="sector.sector" class="">
            <td class="px-4 py-2 border border-gray-300">{{ sector.sector }}</td>
            <td
              class="px-4 py-2 border border-gray-300 font-semibold"
              :class="sector.changesPercentage >= 0 ? 'text-green-600' : 'text-red-600'"
            >
              {{ sector.changesPercentage }}
            </td>
          </tr>
        </tbody>
      </table>
    </div>
  </section>
</template>

<script setup>
defineProps({
  sectors: {
    type: Array,
    required: true
  }
})

function formatPercent(val) {
  if (val === null || val === undefined) return '-'
  return (val >= 0 ? '+' : '') + val.toFixed(2) + '%'
}
</script>

<style scoped>
table {
  font-size: 0.95rem;
}
</style>
