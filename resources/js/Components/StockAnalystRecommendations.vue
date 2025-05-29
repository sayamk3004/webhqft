<script setup>
const props = defineProps({
  recommendations: {
    type: Array,
    default: () => [],
  },
});
</script>

<template>
  <div>
    <h2 class="text-2xl font-semibold mb-4">Analyst Recommendations</h2>

    <table class="w-full table-auto border-collapse border border-gray-300 rounded-md overflow-hidden">
      <thead class="bg-indigo-100">
        <tr>
          <th class="border border-gray-300 px-4 py-2 text-left">Date</th>
          <th class="border border-gray-300 px-4 py-2 text-left">Hold</th>
          <th class="border border-gray-300 px-4 py-2 text-left">Strong Buy</th>
          <th class="border border-gray-300 px-4 py-2 text-left">Strong Sell</th>
          <th class="border border-gray-300 px-4 py-2 text-left">Buy</th>
        </tr>
      </thead>
      <tbody>
        <tr
          v-for="(rec, index) in recommendations"
          :key="index"
          class="hover:bg-indigo-50 cursor-pointer transition-colors duration-150"
        >
          <td class="border border-gray-300 px-4 py-2">{{ new Date(rec.period).toLocaleDateString() }}</td>
          <td class="border border-gray-300 px-4 py-2">{{ rec.hold || 'N/A' }}</td>
          <td class="border border-gray-300 px-4 py-2">{{ rec.strongBuy || 'N/A' }}</td>
          <td class="border border-gray-300 px-4 py-2">{{ rec.strongSell || 'N/A' }}</td>
          <td class="border border-gray-300 px-4 py-2">
{{ rec.buy }}
          </td>
        </tr>
        <tr v-if="recommendations.length === 0">
          <td class="border border-gray-300 px-4 py-2 text-center italic" colspan="5">No recommendations available.</td>
        </tr>
      </tbody>
    </table>
  </div>
</template>
