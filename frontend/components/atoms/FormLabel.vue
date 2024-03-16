<script setup lang="ts">
import {PropType} from "vue";

export interface LabelType {
  for: string,
  text: string,
  isRequired: boolean
}

defineProps({
  label: {
    type: Object as PropType<LabelType>,
    default: () => ({
      for: '',
      text: '',
      isRequired: false
    })
  }
})
</script>

<template>
  <label :for="label.for" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">
    {{ label.text }}
    <span v-if="label.isRequired" class="text-red-700">*</span>
  </label>
</template>