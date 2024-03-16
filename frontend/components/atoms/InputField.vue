<script setup lang="ts">
import {PropType} from "vue";
import {LabelType} from "~/components/atoms/FormLabel.vue";

export interface InputType {
  name: string,
  type: string,
  rows: number,
  required: boolean,
  placeholder: string,
  disabled: boolean,
  class: string,
  label: LabelType,
  selectOptions: string[]
}

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
      :required="input.required"
      :placeholder="input.placeholder"
      :disabled="input.disabled"
      :class="input.class"
      @input="$emit('update:modelValue', $event.target.value)"
  >
</template>