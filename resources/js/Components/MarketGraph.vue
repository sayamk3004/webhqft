<script setup>
import { Chart as ChartJS, Title, Tooltip, Legend, LineElement, LinearScale, PointElement, CategoryScale } from 'chart.js';
import { Line } from 'vue-chartjs';

ChartJS.register(Title, Tooltip, Legend, LineElement, LinearScale, PointElement, CategoryScale);

const props = defineProps({
  marketGraph: Object, // { AAPL: { symbol, data: [...] }, MSFT: {...}, ... }
});

const colors = [
  '#6366f1', '#10b981', '#ef4444', '#3b82f6', '#f59e0b',
  '#a855f7', '#06b6d4', '#e11d48', '#14b8a6', '#f97316'
];

const getLastValidClose = (data = []) => {
  for (let i = data.length - 1; i >= 0; i--) {
    const close = data[i]?.close;
    if (typeof close === 'number' && !isNaN(close)) {
      return close;
    }
  }
  return null;
};

const formatChartData = () => {
  const entries = Object.entries(props.marketGraph ?? {});

  const filtered = entries.filter(([_, stock]) => {
    const lastClose = getLastValidClose(stock.data);
    return lastClose !== null && lastClose > 30;
  });

  if (filtered.length === 0) return { labels: [], datasets: [] };

  // Use first stock for timestamps
  const labels = filtered[0][1].data.map(item => item.timestamp);

  const datasets = filtered.map(([symbol, stock], index) => {
    const closes = stock.data.map(item => item.close ?? null);
    return {
      label: symbol,
      data: closes,
      borderColor: colors[index % colors.length],
      backgroundColor: colors[index % colors.length] + '20',
      tension: 0.2,
      fill: false,
      pointRadius: 0,
    };
  });

  return {
    labels,
    datasets,
  };
};

const chartOptions = {
  responsive: true,
  maintainAspectRatio: false,
  plugins: {
    legend: {
      position: 'top',
    },
    tooltip: {
      mode: 'index',
      intersect: false,
    },
  },
  scales: {
    x: {
      title: {
        display: true,
        text: 'Date',
      },
      ticks: {
        maxTicksLimit: 10,
      },
    },
    y: {
      title: {
        display: true,
        text: 'Closing Price (USD)',
      },
      beginAtZero: false,
    },
  },
};
</script>

<template>
  <div>
    <h2 class="text-2xl font-bold mb-6">Market Trend – Stocks Closing Above $30</h2>
    <div v-if="Object.keys(marketGraph).length === 0" class="text-gray-500 text-center">No chart data available.</div>

    <div v-else class="h-[500px] border p-4 rounded shadow">
      <Line :data="formatChartData()" :options="chartOptions" />
    </div>
  </div>
</template>
