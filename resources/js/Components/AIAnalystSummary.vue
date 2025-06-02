<template>
  <section v-if="aiSummary" class="bg-zinc-900 text-white p-6 rounded-xl shadow-md mb-10">

    <div class="flex justify-between items-center mb-4">
      <h2 class="text-2xl font-bold">AI Analyst Report</h2>
      <span class="text-sm text-gray-400">
        Updated {{ formatDate(aiSummary.published_at) }}
      </span>
    </div>

    <div class="text-gray-200 text-sm leading-relaxed space-y-4">
      <h3 class="text-lg font-semibold text-white">{{ aiSummary.title }}</h3>
      <p>{{ aiSummary.description }}</p>

      <p>
        Source:
        <span class="text-blue-400 font-semibold">{{ aiSummary.source }}</span>
      </p>

      <a
        v-if="aiSummary.url"
        :href="aiSummary.url"
        target="_blank"
        class="text-sm text-blue-500 hover:underline"
      >
        Read Full Article →
      </a>
    </div>
  </section>

  <div v-else class="text-gray-400 text-sm p-4">
    AI Analyst summary is currently unavailable.
  </div>
</template>

<script setup>
const props = defineProps({
  aiSummary: {
    type: Object,
    required: true
  }
});

function formatDate(iso) {
  if (!iso) return '';
  return new Date(iso).toLocaleDateString('en-US', {
    year: 'numeric', month: 'short', day: 'numeric'
  });
}
</script>
