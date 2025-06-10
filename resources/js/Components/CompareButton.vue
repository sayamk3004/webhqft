<template>
  <button
    @click="addToCompare"
    :disabled="compare.symbols.includes(symbol)"
    class="ml-2 px-3 py-2 bg-blue-600 hover:bg-blue-700 text-white text-sm rounded"
  >
      {{ compare.symbols.includes(symbol) ? 'Selected' : 'Compare' }}  
      
  </button>

</template>

<script setup>
import { useCompareStore } from '@/Stores/compare';
const props = defineProps({ symbol: String });

const compare = useCompareStore();

const addToCompare = () => {
  compare.addSymbol(props.symbol);
};
</script>
