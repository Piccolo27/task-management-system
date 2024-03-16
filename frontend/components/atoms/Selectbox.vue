<script setup lang="ts">
import {PropType} from "vue";
import {InputType} from "~/components/atoms/InputField.vue";

defineProps({
  modelValue: {
    type: String,
    default: '',
    require: true
  },
  input: {
    type: Object as PropType<InputType>,
    required: true,
    default: () => ({
      required: false,
      placeholder: '',
      disabled: false,
      class: '',
      label: {},
      selectOptions: []
    })
  }
})

defineEmits(['update:modelValue'])
</script>

<template>
  <select
      :name="input.name"
      :class="input.class"
      @input="$emit('update:modelValue', $event.target.value)"
  >
    <option
        value=""
        :selected="modelValue === ''"
    >
      Choose One
    </option>
    <option
        v-for="(option, index) in input.selectOptions"
        :key="index"
        :value="Object.keys(option)[0]"
        :selected="modelValue == Object.keys(option)[0]"
    >
      {{ option[Object.keys(option)[0]] }}
    </option>
  </select>
</template>