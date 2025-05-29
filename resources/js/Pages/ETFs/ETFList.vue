<template>
  <div class="container mx-auto px-4 py-8">
    <div class="flex justify-between items-center mb-8">
      <h1 class="text-3xl font-bold text-gray-800">ETF Explorer</h1>
      <div class="relative w-64">
        <input
          v-model="searchQuery"
          type="text"
          placeholder="Search ETFs..."
          class="w-full pl-10 pr-4 py-2 rounded-lg border border-gray-300 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
          @input="performSearch"
        >
        <svg
          class="absolute left-3 top-2.5 h-5 w-5 text-gray-400"
          fill="none"
          stroke="currentColor"
          viewBox="0 0 24 24"
          xmlns="http://www.w3.org/2000/svg"
        >
          <path
            stroke-linecap="round"
            stroke-linejoin="round"
            stroke-width="2"
            d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"
          />
        </svg>
      </div>
    </div>

    <div class="border border-gray-800 bg-default text-white shadow-sm rounded-xl shadow-md overflow-hidden">
      <div class="grid grid-cols-12 bg-gray-100 p-4 font-semibold text-gray-600">
        <div class="col-span-3">Symbol</div>
        <div class="col-span-4">Name</div>
        <div class="col-span-2 text-right">Price</div>
        <div class="col-span-3 text-right">Change</div>
      </div>

      <div v-if="loading" class="p-8 text-center">
        <div class="inline-block animate-spin rounded-full h-8 w-8 border-t-2 border-b-2 border-blue-500"></div>
      </div>

      <div v-else>
        <Link
          v-for="etf in paginatedETFs"
          :key="etf.symbol"
          :to="{ name: 'etfs-show', params: { symbol: etf.symbol } }"
          class="grid grid-cols-12 p-4 border-b border-gray-100 hover:bg-blue-50 transition-colors duration-150"
        >
          <div class="col-span-3 font-medium text-blue-600">{{ etf.symbol }}</div>
          <div class="col-span-4 text-gray-700">{{ etf.name }}</div>
          <div class="col-span-2 text-right font-medium">{{ formatCurrency(etf.price) }}</div>
          <div class="col-span-3 text-right" :class="getChangeColor(etf.percentChange)">
            {{ formatChange(etf.change) }} ({{ formatPercent(etf.percentChange) }})
          </div>
        </Link>

        <div v-if="etfs.length === 0" class="p-8 text-center text-gray-500">
          No ETFs found matching your search.
        </div>
      </div>

      <div v-if="etfs.length > 0" class="p-4 flex justify-between items-center border-t border-gray-100">
        <div class="text-sm text-gray-500">
          Showing {{ pagination.from }} to {{ pagination.to }} of {{ pagination.total }} ETFs
        </div>
        <div class="flex space-x-2">
          <button
            @click="prevPage"
            :disabled="pagination.currentPage === 1"
            class="px-3 py-1 rounded-md border"
            :class="pagination.currentPage === 1 ? 'border-gray-200 text-gray-400' : 'border-gray-300 text-gray-700 '"
          >
            Previous
          </button>
          <button
            @click="nextPage"
            :disabled="pagination.currentPage === pagination.lastPage"
            class="px-3 py-1 rounded-md border"
            :class="pagination.currentPage === pagination.lastPage ? 'border-gray-200 text-gray-400' : 'border-gray-300 text-gray-700 '"
          >
            Next
          </button>
        </div>
      </div>
    </div>

    <div class="mt-6 text-xs text-gray-400 text-right">
      Last updated: {{ lastUpdated }}
    </div>
  </div>
</template>

<script>
import { ref, computed, onMounted } from 'vue'
import { Head, Link } from '@inertiajs/vue3';

export default {
  setup() {
    const searchQuery = ref('')
    const loading = ref(true)
    const etfs = ref([])
    const lastUpdated = ref('')
    const currentPage = ref(1)
    const perPage = ref(20)

    const paginatedETFs = computed(() => {
      const start = (currentPage.value - 1) * perPage.value
      const end = start + perPage.value
      return etfs.value.slice(start, end)
    })

    const pagination = computed(() => ({
      currentPage: currentPage.value,
      lastPage: Math.ceil(etfs.value.length / perPage.value),
      total: etfs.value.length,
      from: (currentPage.value - 1) * perPage.value + 1,
      to: Math.min(currentPage.value * perPage.value, etfs.value.length)
    }))

    const performSearch = async () => {
      if (searchQuery.value.length >= 2) {
        loading.value = true
        try {
          const response = await fetch(`/api/etfs/search?q=${searchQuery.value}`)
          etfs.value = await response.json()
          currentPage.value = 1
        } finally {
          loading.value = false
        }
      } else if (searchQuery.value.length === 0) {
        loadPopularETFs()
      }
    }

    const loadPopularETFs = async () => {
      loading.value = true
      try {
        const response = await fetch('/api/etfs')
        const data = await response.json()
        etfs.value = data.etfs
        lastUpdated.value = data.lastUpdated
      } finally {
        loading.value = false
      }
    }

    const nextPage = () => {
      if (currentPage.value < pagination.value.lastPage) {
        currentPage.value++
      }
    }

    const prevPage = () => {
      if (currentPage.value > 1) {
        currentPage.value--
      }
    }

    const getChangeColor = (percent) => {
      if (percent > 0) return 'text-green-600'
      if (percent < 0) return 'text-red-600'
      return 'text-gray-600'
    }

    const formatChange = (value) => {
return value;
    }

    onMounted(() => {
      loadPopularETFs()
    })

    return {
      searchQuery,
      loading,
      etfs,
      lastUpdated,
      paginatedETFs,
      pagination,
      performSearch,
      nextPage,
      prevPage,
      getChangeColor,
      formatChange
    }
  }
}
</script>