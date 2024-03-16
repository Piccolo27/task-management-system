<script setup lang="ts">
import {PropType} from "vue";
import {InputType} from "~/components/atoms/InputField.vue";
import {Checkbox, InputField, Selectbox} from "#components";
import {InputFile} from "#components";
import {Textarea} from "#components";

const props = defineProps({
  modelValue: {
    type: [String, File, Array, Boolean, Number],
    default: ''
  },
  input: {
    type: Object as PropType<InputType>,
    required: true,
    default: () => ({
      name: '',
      type: {
        type: String,
        default: 'text',
        require: true
      },
      rows: {
        type: Number,
        default: 4,
        require: false
      },
      selectOptions: {
        type: Array,
        default: [],
        require: false
      },
      required: false,
      placeholder: '',
      disabled: false,
      class: '',
      label: {}
    })
  },
  errorMessage: {
    type: String,
    default: null
  }
})

defineEmits(['update:modelValue'])

const inputTypeToComponent: Record<string, any> = {
  'file': InputFile,
  'textarea': Textarea,
  'select': Selectbox,
  'checkbox': Checkbox
};

const notComponentTypes = ['date', 'targetPersonSelectBtn', 'disabledBtn']

const dynamicComponent = computed(() => {
  return inputTypeToComponent[props.input?.type] || InputField
})
</script>

<template>
  <FormLabel :label="input.label" />
  <slot name="previewImage" />
  <slot name="date" />
  <slot name="targetPersonSelect" />
  <slot name="disabledButton" />
  <component
    v-if="!notComponentTypes.includes(input.type)"
    :is="dynamicComponent"
    :input="input"
    :model-value="modelValue"
    @update:modelValue="(value) => $emit('update:modelValue', value)"
  />
  <p v-if="errorMessage" id="outlined_error_help" class="mt-2 text-xs text-red-600 dark:text-red-400">
    <span class="font-medium">
      {{ errorMessage }}
    </span>
  </p>
</template>