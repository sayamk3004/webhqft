<template>
  <canvas ref="sectorCanvas"></canvas>
</template>

<script>
import { ref, onMounted, watch, computed } from 'vue'
import Chart from 'chart.js/auto'

export default {
  props: {
    holdings: {
      type: Array,
      required: true
    }
  },
  setup(props) {
    const sectorCanvas = ref(null)
    let chartInstance = null

    const sectorData = computed(() => {
      const sectors = {}
      
      props.holdings.forEach(holding => {
        if (!holding.sector) return
        
        if (!sectors[holding.sector]) {
          sectors[holding.sector] = 0
        }
        sectors[holding.sector] += holding.weight
      })

      return Object.entries(sectors)
        .map(([sector, weight]) => ({ sector, weight }))
        .sort((a, b) => b.weight - a.weight)
    })

    const createChart = () => {
      if (chartInstance) {
        chartInstance.destroy()
      }

      if (sectorCanvas.value && sectorData.value.length > 0) {
        const ctx = sectorCanvas.value.getContext('2d')
        const colors = [
          '#3B82F6', '#10B981', '#F59E0B', '#EF4444', '#8B5CF6',
          '#EC4899', '#14B8A6', '#F97316', '#64748B', '#84CC16'
        ]

        chartInstance = new Chart(ctx, {
          type: 'doughnut',
          data: {
            labels: sectorData.value.map(item => item.sector),
            datasets: [{
              data: sectorData.value.map(item => item.weight),
              backgroundColor: colors,
              borderWidth: 0
            }]
          },
          options: {
            responsive: true,
            maintainAspectRatio: false,
            cutout: '70%',
            plugins: {
              legend: {
                position: 'right',
                labels: {
                  boxWidth: 12,
                  padding: 16,
                  usePointStyle: true,
                  pointStyle: 'circle'
                }
              },
              tooltip: {
                callbacks: {
                  label: function(context) {
                    return `${context.label}: ${context.raw.toFixed(1)}%`
                  }
                }
              }
            }
          }
        })
      }
    }

    onMounted(() => {
      createChart()
    })

    watch(sectorData, () => {
      createChart()
    }, { deep: true })

    return {
      sectorCanvas
    }
  }
}
</script>