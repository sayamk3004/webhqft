<template>
  <AppLayout>
    <div class="max-w-7xl mx-auto px-4 py-10 text-white">
      <h2 class="text-4xl font-extrabold mb-10 text-center tracking-tight">📊 Stock Comparison Dashboard</h2>
      <div class="bg-gradient-to-br from-gray-800 to-gray-900 p-8 rounded-2xl shadow-2xl mb-12">
        <h3 class="text-2xl font-semibold mb-6 border-b border-gray-700 pb-2">💹 Price Bar Chart</h3>
        <BarChart :labels="symbols" :values="symbols.map(s => data[s]?.price || 0)" />
      </div>
      <div class="overflow-x-auto rounded-2xl bg-gray-900 p-6 shadow-xl">
        <table class="min-w-full text-sm text-left border border-gray-700">
          <thead class="bg-gray-800 text-gray-100">
            <tr>
              <th class="p-4 border border-gray-700 text-white tracking-wide font-semibold">Metric</th>
              <th
                v-for="symbol in symbols"
                :key="symbol"
                class="p-4 border border-gray-700 text-white tracking-wide font-semibold whitespace-nowrap"
              >
                {{ data[symbol]?.name || symbol }}
              </th>
            </tr>
          </thead>
          <tbody class="text-white-300">
            <tr
              v-for="m in metrics"
              :key="m.key"
              class="hover:bg-gray-800 transition duration-150 border-t border-gray-700 text-white"
            >
              <td class="p-4 font-semibold border border-gray-700 text-white">{{ m.label }}</td>
              <td
                v-for="symbol in symbols"
                :key="symbol + m.key"
                class="p-4 border border-gray-700 text-white"
              >
                {{ format(data[symbol]?.[m.key]) }}
              </td>
            </tr>
          </tbody>
        </table>
      </div>
    </div>
  </AppLayout> 
</template>

<script setup>
import AppLayout from '@/Layouts/AppLayout.vue';
import { defineProps } from 'vue';
import BarChart from '@/Components/Charts/BarChart.vue';

const props = defineProps({
  symbols: Array,
  data: Object,
});

const metrics = [
  { key: 'price', label: '💰 Current Price ($)' },
  { key: 'change', label: '📈 Change ($)' },
  { key: 'percentChange', label: '📊 Change %' },
  { key: 'marketCap', label: '🏢 Market Cap (B)' },
  { key: 'peRatio', label: '🧮 P/E Ratio' },
  { key: 'industry', label: '🏭 Industry' },
];

const format = (val) =>
  val !== undefined && val !== null
    ? typeof val === 'number'
      ? val.toLocaleString(undefined, { maximumFractionDigits: 2 })
      : val
    : '—';
</script>

<style scoped>
table th,
table td {
  white-space: nowrap;
}
</style>
