<template>
  <div v-if="compare.symbols.length" class="fixed bottom-5 right-5 z-50 bg-gray-800 text-white px-4 py-3 rounded-xl shadow-lg">
    <div class="flex items-center gap-4">
      <span class="font-semibold">Compare ({{ compare.symbols.length }})</span>
      <button @click="goToCompare" class="bg-green-600 px-3 py-1 rounded text-sm hover:bg-green-700">Go</button>
      <button @click="compare.clear" class="text-red-400 text-xs hover:text-red-200">Clear</button>
    </div>
  </div>
</template>

<script setup>
import { router } from '@inertiajs/vue3';
import { useCompareStore } from '@/Stores/compare';

const compare = useCompareStore();

const goToCompare = () => {
  const symbols = compare.symbols.join(',');
  alert(symbols);
  router.get(route('compare.show', symbols));
};
</script>
