<script setup>
const props = defineProps({
  peers: {
    type: Array,
    default: () => [],
  },
});
</script>

<template>
  <div>
    <h2 class="text-2xl font-semibold mb-4">Peer Companies</h2>
    <ul class="grid grid-cols-2 md:grid-cols-4 gap-4">
      <li v-for="(peer, index) in peers" :key="index" class="p-4 border rounded-lg text-center hover:bg-indigo-50 cursor-pointer transition-colors duration-200">
        <div class="font-semibold text-indigo-700">{{ peer.symbol }}</div>
        <div class="text-gray-600 text-sm mt-1">{{ peer.score }}</div>
      </li>
      <li v-if="peers.length === 0" class="text-gray-500 italic col-span-full text-center">No peers available.</li>
    </ul>
  </div>
</template>
