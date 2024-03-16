<script setup lang="ts">
import {PropType} from "vue";
import {InputType} from "~/components/atoms/InputField.vue";

defineProps({
  modelValue: {
    type: String,
    require: true
  },
  label: {
    type: String,
    default: '',
    require: true
  },
  input: {
    type: Object as PropType<InputType>,
    require: true
  }
})
</script>

<template>
  <div class="pb-4 flex items-center">
    <label for="table-search" class="mr-3">{{ label }}</label>
    <InputField
        :model-value="modelValue"
        :input="input"
        @update:modelValue="(value) => $emit('update:modelValue', value)"
    />
  </div>
</template>