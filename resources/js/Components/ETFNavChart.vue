<template>
  <div class="bg-gray-900 rounded-xl p-4 shadow-md">
    <h2 class="text-xl font-semibold text-white mb-2">1-Month NAV Chart: {{ symbol }}</h2>
    <LineChart :chart-data="chartData" :chart-options="chartOptions" />
  </div>
</template>

<script setup>
import { computed } from 'vue';
import { Line as LineChart } from 'vue-chartjs';
import {
  Chart as ChartJS,
  Title,
  Tooltip,
  Legend,
  LineElement,
  CategoryScale,
  LinearScale,
  PointElement,
  Filler,
} from 'chart.js';

ChartJS.register(Title, Tooltip, Legend, LineElement, CategoryScale, LinearScale, PointElement, Filler);

const props = defineProps({
  navData: {
    type: Array,
    required: true,
  },
  symbol: {
    type: String,
    required: true,
  },
});

const chartData = computed(() => ({
  labels: props.navData.map(point => point.date),
  datasets: [
    {
      label: `${props.symbol} NAV`,
      data: props.navData.map(point => point.close),
      fill: true,
      backgroundColor: 'rgba(59, 130, 246, 0.1)',
      borderColor: '#3b82f6',
      pointRadius: 0,
      tension: 0.4,
    },
  ],
}));

const chartOptions = {
  responsive: true,
  maintainAspectRatio: false,
  scales: {
    x: {
      ticks: {
        color: '#9ca3af',
        maxTicksLimit: 6,
      },
      grid: {
        display: false,
      },
    },
    y: {
      ticks: {
        color: '#9ca3af',
        callback: value => `$${value}`,
      },
      grid: {
        color: 'rgba(255, 255, 255, 0.05)',
      },
    },
  },
  plugins: {
    legend: {
      display: false,
    },
    tooltip: {
      mode: 'index',
      intersect: false,
      callbacks: {
        label: context => `$${context.parsed.y.toFixed(2)}`,
      },
    },
  },
};
</script>

<style scoped>
div {
  height: 300px;
}
</style>
