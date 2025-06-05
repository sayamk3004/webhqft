<template>
  <section class="market-indices p-4 border border-gray-800 bg-default text-white shadow-sm rounded shadow">
    <h2 class="text-xl font-semibold mb-4">Market Indices</h2>
    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">

      <div v-for="index in indices" :key="index.symbol"
        class="index-card p-4 border rounded hover:shadow-md transition">
        <div class="flex justify-between items-center mb-2">

          <Link :href="route('indices.show', { symbol: index.symbol })"
            class="flex flex-col text-left hover:underline cursor-pointer">
          <h3 class="font-bold">{{ index.name || index.symbol }}</h3>
          </Link>
          <span :class="{ 'text-green-600': index.changes >= 0, 'text-red-600': index.changes < 0 }">
            {{ formatChange(index.changes) }} ({{ formatPercent(index.changesPercentage) }})
          </span>
        </div>
        <p class="text-lg font-mono">{{ formatPrice(index.price) }}</p>
      </div>
    </div>
  </section>
</template>

<script setup>
import { Link } from '@inertiajs/vue3'
import { toDisplayString } from 'vue'

defineProps({
  indices: {
    type: Array,
    required: true
  }
})

function formatPrice(val) {
  return val ? `$${val.toFixed(2)}` : '-'
}

function formatChange(val) {
  if (val === null || val === undefined) return '-'
  return (val >= 0 ? '+' : '') + val.toFixed(2)
}

function formatPercent(val) {
  if (val === null || val === undefined) return '-'
  return (val >= 0 ? '+' : '') + val.toFixed(2) + '%'
}
</script>

<style scoped>
.index-card {
  min-width: 200px;
}
</style>
