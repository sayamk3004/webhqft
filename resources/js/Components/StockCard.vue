<script setup>
import { Link } from '@inertiajs/vue3';
defineProps({
    stock: Object,
});
</script>

<template>
    <div class="border border-gray-800 bg-default text-white shadow-sm rounded-lg shadow p-4">
        <div class="flex justify-between items-start">
            <div>
                <h3 class="font-bold text-lg">                           <Link :href="route('stocks.show', stock.symbol)" class="text-xl sm:text-2xl tex-white font-semibold font-medium">
                                        {{ stock.symbol }}
                                    </Link></h3>
                <p class="text-gray-600 text-sm">{{ stock.name }}</p>
                
            </div>
            <div class="text-right">
                <div class="font-bold">${{ stock.price.toFixed(2) }}</div>
                <div
                    :class="{
                        'text-green-500': stock.change >= 0,
                        'text-red-500': stock.change < 0,
                    }"
                    class="text-sm"
                >
                    {{ stock.change >= 0 ? '+' : '' }}{{ stock.change.toFixed(2) }} ({{ stock.percentChange.toFixed(2) }}%)
                </div>
            </div>
        </div>
    </div>
</template>