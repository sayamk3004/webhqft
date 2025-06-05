<script setup>
const props = defineProps({
  peers: {
    type: Array,
    default: () => [],
  },
});
</script>

<template>
  <div class="p-6  shadow-lg rounded-lg">
    <h2 class="text-2xl font-semibold text-white-800 mb-6">Peer Companies</h2>
    <ul class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-6">
      <li 
        v-for="(peer, index) in peers" 
        :key="index" 
        class="p-4 border rounded-lg text-center hover:bg-[#FF2D20] hover:text-white cursor-pointer transition-colors duration-200"
      >
        <div class="font-semibold text-indigo-700">{{ peer.symbol }}</div>
        <div class="text-white-600 text-sm mt-1">Score: {{ peer.score.toFixed(4) }}</div>
      </li>
      <li v-if="peers.length === 0" class="text-white-500 italic col-span-full text-center">No peers available.</li>
    </ul>
  </div>
</template>
