<script setup lang="ts">
import {PropType} from "vue";
import {LabelType} from "~/components/atoms/FormLabel.vue";
import {InputType} from "~/components/atoms/InputField.vue";

defineProps({
  modelValue: {
    type: [String],
    default: ''
  },
  input: {
    type: Object as PropType<InputType>,
    required: true,
    default: () => ({
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
  <textarea
    :class="input.class"
    :rows="input.rows"
    :required="input.required"
    :disabled="input.disabled"
    @input="$emit('update:modelValue', $event.target.value)"
  >{{ modelValue }}</textarea>
</template>