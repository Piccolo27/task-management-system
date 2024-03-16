<script setup lang="ts">
defineProps({
  dropdownId: {
    type: String,
    require: true
  }
})
</script>

<template>
  <div
      :id="dropdownId"
      class="z-10 hidden font-normal bg-white divide-y divide-gray-100 rounded-lg shadow w-44 dark:bg-gray-700 dark:divide-gray-600"
  >
    <ul class="py-2 text-sm text-gray-700 dark:text-gray-400" aria-labelledby="dropdownLargeButton">
      <slot />
    </ul>
  </div>
</template>