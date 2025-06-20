<script setup>
import { Head, Link } from '@inertiajs/vue3';
import AppLayout from '@/Layouts/AppLayout.vue';
import Pagination from '@/Components/Pagination.vue';

defineProps({
    etfs: Array,
    search: String,
    pagination: Object,
});
</script>

<template>

    <Head title="ETFs" />

    <AppLayout>
        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div class="mb-6 flex justify-between items-center">
                    <h1 class="text-3xl font-bold">ETFs</h1>
                    <!-- <div class="relative w-64">
                        <input type="text" placeholder="Search ETFs..."
                            class="w-full px-4 py-2 border text-black border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500"
                            :value="search"
                            @input="$inertia.get(route('etfs.index'), { search: $event.target.value }, { preserveState: true })" />
                    </div> -->
                </div>

                <div
                    class="border border-gray-800 bg-default text-white shadow-sm overflow-hidden shadow-sm sm:rounded-lg">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th
                                    class="px-6 py-3 text-left text-xs font-medium text-white-500 uppercase tracking-wider">
                                    Symbol</th>
                                <th
                                    class="px-6 py-3 text-left text-xs font-medium text-white-500 uppercase tracking-wider">
                                    Name</th>
                                <th
                                    class="px-6 py-3 text-left text-xs font-medium text-white-500 uppercase tracking-wider">
                                    Price</th>
                                <th
                                    class="px-6 py-3 text-left text-xs font-medium text-white-500 uppercase tracking-wider">
                                    % Change</th>

                                <th
                                    class="px-6 py-3 text-left text-xs font-medium text-white-500 uppercase tracking-wider">
                                    Dividend Yield </th>
                            </tr>
                        </thead>
                        <tbody class="border border-gray-800 bg-default text-white shadow-sm divide-y divide-gray-200">
                            <tr v-for="etf in etfs" :key="etf.symbol">
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <Link :href="route('etfs.show', etf.symbol)"
                                        class="text-xl sm:text-2xl text-white font-semibold">
                                    {{ etf.symbol }}
                                    </Link>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm text-white-900">{{ etf.shortName }}</div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm text-white-900">{{ etf.currency }}{{
                                        etf.regularMarketPrice?.toFixed(2) || 'N/A' }}</div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div :class="{
                                        'text-green-600': etf.regularMarketChangePercent >= 0,
                                        'text-red-600': etf.regularMarketChangePercent < 0,
                                    }">
                                        {{ etf.regularMarketChangePercent >= 0 ? '+' : '' }}{{
                                            etf.regularMarketChangePercent?.toFixed(2) || 'N/A'
                                        }}%
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div>
                                        {{ etf.dividendYield }}
                                    </div>
                                </td>
                            </tr>
                            <tr v-if="etfs.length === 0">
                                <td colspan="5" class="px-6 py-4 text-center text-white-500">No ETFs found</td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <Pagination :pagination="pagination" class="mt-6" />
            </div>
        </div>
    </AppLayout>
</template>
