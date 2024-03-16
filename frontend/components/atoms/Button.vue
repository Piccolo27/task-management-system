<script setup lang="ts">
import {PropType} from "vue";

export interface ButtonType {
  type: string
  class: string
  text?: string
  dropdownId?: string
  modelId?: string,
  dismissTarget?: string
}

defineProps({
  button: {
     type: Object as PropType<ButtonType>,
     require: true,
     default: () => ({
       type: 'button',
       class: '',
       dropdownId: ''
     })
  },
  disabled: {
    type: Boolean,
    default: false
  }
})
</script>

<template>
  <button
      :type="button.type"
      :class="button.class"
      :disabled="disabled"
      :data-dropdown-toggle="button.dropdownId"
      :data-modal-hide="button.modelId"
      :data-dismiss-target="button.dismissTarget"
      @click="$emit('click')"
  >
    <slot />
  </button>
</template>