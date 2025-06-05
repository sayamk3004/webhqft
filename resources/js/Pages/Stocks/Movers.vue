<script setup>
import { Head, Link } from '@inertiajs/vue3';
import AppLayout from '@/Layouts/AppLayout.vue';
import TopMovers from '@/Components/TopMovers.vue';

defineProps({
    gainers: Array,
    losers: String
});
</script>

<template>
    <Head title="Stocks" />

    <AppLayout>
        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">

                <TopMovers :gainers="gainers" :losers="losers" class="mb-8" />

            </div>
        </div>
    </AppLayout>
</template>