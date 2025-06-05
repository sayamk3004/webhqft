<template>
  <div class="bg-grey shadow rounded-2xl p-6 mt-6">
    <h2 class="text-xl font-bold mb-4">Performance (Last 90 Days)</h2>
    <div v-if="chartData.length">
      <StockChart
        :data="chartData.map(d => d.close)"
        :labels="chartData.map(d => d.date)"
        label="Closing Price"
      />
    </div>
    <p v-else class="text-gray-500">No performance data available.</p>
  </div>
</template>

<script setup>
import { computed } from 'vue';
import StockChart from './StockChart.vue';

const props = defineProps({
  performanceData: Array,
});

const chartData = computed(() => {
  if (!Array.isArray(props.performanceData)) return [];
  return props.performanceData.slice(-90).map(item => ({
    date: item.date,
    close: item.close,
  }));
});
</script>
