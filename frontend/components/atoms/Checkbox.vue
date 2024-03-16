<script setup lang="ts">
import {PropType} from "vue";
import {InputType} from "~/components/atoms/InputField.vue";

defineProps({
  modelValue: {
    type: [String, Boolean],
    default: ''
  },
  input: {
    type: Object as PropType<InputType>,
    required: true,
    default: () => ({
      name: '',
      type: 'text',
      required: false,
      placeholder: '',
      disabled: false,
      class: '',
      label: {}
    })
  }
})

defineEmits(['update:modelValue'])
</script>

<template>
  <input
      :name="input.name"
      :value="modelValue"
      :type="input.type"
      :disabled="input.disabled"
      :class="input.class"
      @input="$emit('update:modelValue', $event.target.checked)"
  >
</template>