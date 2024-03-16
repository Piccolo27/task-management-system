<script setup lang="ts">
import {PropType} from "vue";
import {ButtonType} from "~/components/atoms/Button.vue";
import {DropdownItem} from "~/components/atoms/DropdownList.vue";

const {button, dropdownItems, isShow} = defineProps({
  button: {
    type: Object as PropType<ButtonType>,
    require: true
  },
  dropdownItems: {
    type: Array as PropType<DropdownItem[]>,
    require: true
  },
  isShow: {
    type: Boolean,
    default: true,
    require: true
  }
})

button['class'] = 'flex items-center justify-between w-full py-2 pl-3 pr-4 text-gray-900 rounded hover:bg-gray-100 ' +
    'md:hover:bg-transparent md:border-0 md:hover:text-blue-700 md:p-0 md:w-auto dark:text-white md:dark:hover:text-blue-500 ' +
    'dark:focus:text-white dark:border-gray-700 dark:hover:bg-gray-700 md:dark:hover:bg-transparent'
</script>

<template>
  <li>
    <Button
      v-if="isShow"
      :button="button"
      :disabled="false"
    >
      {{ button.text }}
      <svg class="w-2.5 h-2.5 ml-2.5" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none"
           viewBox="0 0 10 6">
        <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
              d="m1 1 4 4 4-4"/>
      </svg>
    </Button>
    <DropdownMenu :dropdown-id="button.dropdownId">
      <DropdownList v-for="(item, index) in dropdownItems" :key="index" :item="item" />
    </DropdownMenu>
  </li>
</template>