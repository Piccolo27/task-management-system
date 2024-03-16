<script setup lang="ts">
import {PropType} from "vue";

export interface InputFileType {
  name: string,
  required: boolean,
  disabled: boolean,
  class: string,
}

defineProps({
  modelValue: {
    type: Object as PropType<File | null>,
    require: true
  },
  input: {
    type: Object as PropType<InputFileType>,
    required: true,
    default: () => ({
      name: '',
      disabled: false,
      class: '',
      label: {}
    })
  }
})
</script>

<template>
  <input
      type="file"
      :class="input.class"
      :disabled="input.disabled"
      @input="$emit('update:modelValue', $event.target.files[0])"
  />
</template>