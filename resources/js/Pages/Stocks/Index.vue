<script setup>
import { Head, Link } from '@inertiajs/vue3';
import AppLayout from '@/Layouts/AppLayout.vue';
import Pagination from '@/Components/Pagination.vue';

defineProps({
    stocks: Array,
    search: String,
    pagination: Object,
});
</script>

<template>
    <Head title="Stocks" />

    <AppLayout>
        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div class="mb-6 flex justify-between items-center">
                    <h1 class="text-3xl font-bold">Stocks</h1>
                    <div class="relative w-64">
                        <input
                            type="text"
                            placeholder="Search stocks..."
                            class="w-full px-4 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500"
                            :value="search"
                            @input="$inertia.get(route('stocks.index'), { search: $event.target.value }, { preserveState: true })"
                        />
                        <button
                            @click="$inertia.get(route('stocks.index'), { search })"
                            class="absolute right-2 top-2 text-gray-400 hover:text-gray-600"
                        >
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                            </svg>
                        </button>
                    </div>
                </div>

                <div class="border border-gray-800 bg-default text-white shadow-sm overflow-hidden shadow-sm sm:rounded-lg">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Symbol</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Company</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Price</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Change</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">% Change</th>
                            </tr>
                        </thead>
                        <tbody class="border border-gray-800 bg-default text-white shadow-sm divide-y divide-gray-200">
                            <tr v-for="stock in stocks" :key="stock.symbol" class="">
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <Link :href="route('stocks.show', stock.symbol)" class="text-xl sm:text-2xl tex-white font-semibold font-medium">
                                        {{ stock.symbol }}
                                    </Link>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm text-gray-900">{{ stock.description }}</div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm text-gray-900">${{ stock.price?.toFixed(2) || 'N/A' }}</div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div :class="{
                                        'text-green-600': stock.change >= 0,
                                        'text-red-600': stock.change < 0,
                                    }">
                                        {{ stock.change >= 0 ? '+' : '' }}{{ stock.change?.toFixed(2) || 'N/A' }}
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div :class="{
                                        'text-green-600': stock.percentChange >= 0,
                                        'text-red-600': stock.percentChange < 0,
                                    }">
                                        {{ stock.percentChange >= 0 ? '+' : '' }}{{ stock.percentChange?.toFixed(2) || 'N/A' }}%
                                    </div>
                                </td>
                            </tr>
                            <tr v-if="stocks.length === 0">
                                <td colspan="5" class="px-6 py-4 text-center text-gray-500">No stocks found</td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <Pagination :pagination="pagination" class="mt-6" />
            </div>
        </div>
    </AppLayout>
</template>