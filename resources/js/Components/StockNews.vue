<script setup>
const props = defineProps({
  news: {
    type: Array,
    default: () => [],
  },
});
</script>

<template>
  <div>
    <h2 class="text-2xl font-semibold mb-4">Latest News</h2>
    <ul class="space-y-4">
      <li v-for="(item, index) in news" :key="index" class="p-4 border rounded-lg hover:shadow-lg transition-shadow duration-200">

        <a :href="item.link" target="_blank" rel="noopener noreferrer" class="text-white-600 hover:underline font-semibold">
          {{ item.title }}
        </a>
        <p class="text-grey-600 mt-1 text-sm">{{ item.publisher }}</p>
        <p class="text-grey-500 mt-1 text-xs">{{ new Date(item.published_at).toLocaleString() }}</p>
        <p class="mt-2 text-white-700" v-if="item.summary">{{ item.summary }}</p>
      </li>
      <li v-if="news.length === 0" class="text-white-500 italic">No news available.</li>
    </ul>
  </div>
</template>
