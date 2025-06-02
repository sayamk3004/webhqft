<template>
  <canvas ref="chartCanvas"></canvas>
</template>

<script>
import { ref, onMounted, watch } from 'vue'
import Chart from 'chart.js/auto'

export default {
  props: {
    data: {
      type: Object,
      required: true
    }
  },
  setup(props) {
    const chartCanvas = ref(null)
    let chartInstance = null

    const createChart = () => {
      if (chartInstance) {
        chartInstance.destroy()
      }

      if (chartCanvas.value && props.data.labels.length > 0) {
        const ctx = chartCanvas.value.getContext('2d')
        
        chartInstance = new Chart(ctx, {
          type: 'line',
          data: {
            labels: props.data.labels,
            datasets: [{
              label: 'Price',
              data: props.data.prices,
              borderColor: '#3B82F6',
              backgroundColor: 'rgba(59, 130, 246, 0.1)',
              borderWidth: 2,
              fill: true,
              tension: 0.1,
              pointRadius: 0
            }]
          },
          options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
              legend: {
                display: false
              },
              tooltip: {
                mode: 'index',
                intersect: false,
                callbacks: {
                  label: function(context) {
                    return `$${context.parsed.y.toFixed(2)}`
                  }
                }
              }
            },
            scales: {
              x: {
                grid: {
                  display: false
                },
                ticks: {
                  maxRotation: 0,
                  autoSkip: true,
                  maxTicksLimit: 6
                }
              },
              y: {
                position: 'right',
                ticks: {
                  callback: function(value) {
                    return `$${value}`
                  }
                }
              }
            },
            interaction: {
              mode: 'nearest',
              axis: 'x',
              intersect: false
            }
          }
        })
      }
    }

    onMounted(() => {
      createChart()
    })

    watch(() => props.data, () => {
      createChart()
    }, { deep: true })

    return {
      chartCanvas
    }
  }
}
</script>