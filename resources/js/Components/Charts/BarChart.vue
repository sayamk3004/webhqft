<template>
  <Bar :data="barData" :options="options" />
</template>

<script setup>
import { Bar } from 'vue-chartjs';
import {
  Chart as ChartJS,
  Title,
  Tooltip,
  Legend,
  BarElement,
  CategoryScale,
  LinearScale,
} from 'chart.js';

ChartJS.register(Title, Tooltip, Legend, BarElement, CategoryScale, LinearScale);

const props = defineProps({
  labels: Array,
  values: Array,
});

const barData = {
  labels: props.labels,
  datasets: [
    {
      label: 'Price ($)',
      backgroundColor: '#10b981',
      borderRadius: 6,
      data: props.values,
    },
  ],
};

const options = {
  responsive: true,
  plugins: {
    legend: { display: false },
  },
  scales: {
    y: {
      ticks: { color: '#ccc' },
      grid: { color: '#444' },
    },
    x: {
      ticks: { color: '#ccc' },
      grid: { color: '#444' },
    },
  },
};
</script>
