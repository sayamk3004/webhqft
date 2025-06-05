<script setup>
defineProps({ distributions: Array });
</script>

<template>
  <div>
    <h2 class="text-xl font-bold mb-4">Dividend History</h2>
    <table class="min-w-full divide-y divide-gray-200 border border-gray-800 bg-default text-white shadow-sm">
      <thead class="bg-gray-50 text-gray-600">
        <tr>
          <th class="px-4 py-2 text-left">Date</th>
          <th class="px-4 py-2 text-left">Dividend</th>
        </tr>
      </thead>
      <tbody>
        <tr v-for="(item, index) in distributions" :key="index">
          <td class="px-4 py-2">{{ item.date }}</td>
          <td class="px-4 py-2">{{ item.dividend?.toFixed(2) }}</td>
        </tr>
        <tr v-if="distributions.length === 0">
          <td colspan="2" class="px-4 py-2 italic text-center">No dividend history available.</td>
        </tr>
      </tbody>
    </table>
  </div>
</template>
