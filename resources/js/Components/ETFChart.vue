<script setup>
import { Line } from 'vue-chartjs';
import {
  Chart as ChartJS,
  Title,
  Tooltip,
  Legend,
  LineElement,
  LinearScale,
  PointElement,
  CategoryScale,
} from 'chart.js';

ChartJS.register(Title, Tooltip, Legend, LineElement, LinearScale, PointElement, CategoryScale);

const props = defineProps({
  symbol: String,
  chartData: Array, // this should now be an array of objects
});

const chartOptions = {
  responsive: true,
  maintainAspectRatio: false,
  plugins: {
    legend: {
      display: false,
    },
    tooltip: {
      mode: 'index',
      intersect: false,
    },
  },
  scales: {
    x: {
      grid: {
        display: false,
      },
    },
    y: {
      grid: {
        color: '#e5e7eb',
      },
    },
  },
};

const formatChartData = () => {
  if (!Array.isArray(props.chartData) || props.chartData.length === 0) {
    return {
      labels: [],
      datasets: [],
    };
  }

  const labels = props.chartData.map(item => item.timestamp);
  const data = props.chartData.map(item => item.close); // using closing price for chart

  return {
    labels,
    datasets: [
      {
        label: `${props.symbol} Closing Price`,
        data,
        borderColor: '#4f46e5',
        backgroundColor: 'rgba(79, 70, 229, 0.1)',
        tension: 0.1,
        fill: true,
        pointRadius: 0,
      },
    ],
  };
};
</script>

<template>
  <div>
    <h2 class="text-xl font-bold mb-4">{{ symbol }} Price Chart (1 Year)</h2>
    <div class="h-96">
      <Line :data="formatChartData()" :options="chartOptions" />
    </div>
  </div>
</template>
