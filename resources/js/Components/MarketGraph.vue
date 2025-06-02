<template>
  <section class="bg-white dark:bg-zinc-900 text-black dark:text-white p-6 rounded-xl shadow-md mb-10">
    <h2 class="text-2xl font-bold mb-6">📈 Market Graph – 1 Month Trend</h2>
    <canvas ref="chartRef" class="w-full h-96"></canvas>
  </section>
</template>

<script setup>
import { onMounted, ref, watch } from 'vue';
import Chart from 'chart.js/auto';

const props = defineProps({
  marketGraph: Object
});

const chartRef = ref(null);

function formatLabel(dateTs) {
  return new Date(dateTs * 1000).toLocaleDateString(undefined, {
    month: 'short',
    day: 'numeric'
  });
}

onMounted(() => {
  if (!props.marketGraph || Object.keys(props.marketGraph).length === 0) return;

  const datasets = [];
  let labels = [];

  for (const symbol in props.marketGraph) {
    const stock = props.marketGraph[symbol];
    if (!stock || !stock.timestamps || !stock.closes) continue;

    const dates = stock.timestamps.map(ts => formatLabel(ts));
    if (labels.length === 0) labels = dates;

    datasets.push({
      label: symbol,
      data: stock.closes,
      borderWidth: 2,
      pointRadius: 0,
      tension: 0.3,
    });
  }

  new Chart(chartRef.value, {
    type: 'line',
    data: {
      labels,
      datasets,
    },
    options: {
      responsive: true,
      interaction: {
        mode: 'index',
        intersect: false,
      },
      plugins: {
        legend: {
          position: 'bottom',
          labels: {
            color: '#d1d5db',
            boxWidth: 12,
            padding: 15,
          },
        },
        tooltip: {
          mode: 'index',
          intersect: false,
          backgroundColor: '#1f2937',
          titleColor: '#ffffff',
          bodyColor: '#f9fafb',
        },
      },
      scales: {
        x: {
          ticks: {
            color: '#9ca3af',
            autoSkip: true,
            maxTicksLimit: 10
          },
          grid: {
            color: '#374151'
          }
        },
        y: {
          ticks: {
            color: '#9ca3af'
          },
          grid: {
            color: '#374151'
          }
        }
      }
    }
  });
});
</script>

<style scoped>
canvas {
  max-width: 100%;
}
</style>
