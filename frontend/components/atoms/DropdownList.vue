<script setup lang="ts">
import {PropType} from "vue";

export interface DropdownItem {
  linkTo: string
  class: string
  text: string
}

defineProps({
  item: {
    type: Object as PropType<DropdownItem>,
    require: true,
    default: () => ({
      linkTo: {
        type: String,
        require: true
      },
      class: {
        type: String,
        require: true,
        default: ''
      },
      text: {
        type: String,
        require: true,
        default: ''
      }
    })
  }
})
</script>

<template>
  <li>
    <NuxtLink
        :to="item.linkTo"
        :class="item.class"
    >
      {{ item.text }}
    </NuxtLink>
  </li>
</template>