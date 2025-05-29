<template>
  <section class="economic-calendar p-4 border border-gray-800 bg-default text-white shadow-sm rounded shadow mt-8">
    <h2 class="text-xl font-semibold mb-4">Economic Calendar</h2>
    <div class="overflow-x-auto max-h-96">
      <table class="w-full text-left border-collapse border border-gray-200">
        <thead>
          <tr class="bg-gray-100">
            <th class="px-4 py-2 border border-gray-300">Date</th>
            <th class="px-4 py-2 border border-gray-300">Event</th>
            <th class="px-4 py-2 border border-gray-300">Country</th>
            <th class="px-4 py-2 border border-gray-300">Impact</th>
            <th class="px-4 py-2 border border-gray-300">Previous</th>
            <th class="px-4 py-2 border border-gray-300">Forecast</th>
            <th class="px-4 py-2 border border-gray-300">Actual</th>
          </tr>
        </thead>
        <tbody>
          <tr v-for="event in calendar" :key="event.id" class="">
            <td class="px-4 py-2 border border-gray-300">{{ formatDate(event.date) }}</td>
            <td class="px-4 py-2 border border-gray-300">{{ event.event }}</td>
            <td class="px-4 py-2 border border-gray-300">{{ event.country }}</td>
            <td class="px-4 py-2 border border-gray-300 font-semibold" :class="impactClass(event.impact)">
              {{ event.impact }}
            </td>
            <td class="px-4 py-2 border border-gray-300">{{ event.previous || '-' }}</td>
            <td class="px-4 py-2 border border-gray-300">{{ event.forecast || '-' }}</td>
            <td class="px-4 py-2 border border-gray-300">{{ event.actual || '-' }}</td>
          </tr>
        </tbody>
      </table>
    </div>
  </section>
</template>

<script setup>
import { format, parseISO } from 'date-fns'

defineProps({
  calendar: {
    type: Array,
    required: true
  }
})

function formatDate(dateStr) {
  try {
    return format(parseISO(dateStr), 'MMM dd, yyyy HH:mm')
  } catch {
    return dateStr
  }
}

function impactClass(impact) {
  switch (impact?.toLowerCase()) {
    case 'high': return 'text-red-600 font-bold'
    case 'medium': return 'text-yellow-600 font-semibold'
    case 'low': return 'text-green-600'
    default: return ''
  }
}
</script>

<style scoped>
table {
  font-size: 0.9rem;
}
</style>
