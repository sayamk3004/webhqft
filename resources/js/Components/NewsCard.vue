<script setup>
import dayjs from 'dayjs'

defineProps({
  news: {
    type: Object,
    required: true,
  },
});
</script>

<template>
  <div class="border border-gray-800 bg-default text-white shadow-sm rounded-2xl shadow-md overflow-hidden transition hover:shadow-lg">
    <img
      v-if="news.image"
      :src="news.image"
      :alt="news.title"
      class="w-full h-52 object-cover"
    />
    <div class="p-5 space-y-3">
      <div class="flex items-center justify-between text-xs text-gray-500">
        <span>{{ news.site }}</span>
        <span>{{ dayjs(news.publishedDate).format('MMM D, YYYY h:mm A') }}</span>
      </div>

      <h2 class="text-lg font-semibold text-white leading-snug">
        {{ news.title }}
      </h2>

      <p class="text-sm text-gray-600">
        {{ news.text?.substring(0, 160) }}...
      </p>

      <div>
        <a
          :href="news.url"
          target="_blank"
          class="inline-block text-indigo-600 text-sm font-medium hover:underline"
          rel="noopener noreferrer"
        >
          Read full article →
        </a>
      </div>
    </div>
  </div>
</template>

<style scoped>
</style>
