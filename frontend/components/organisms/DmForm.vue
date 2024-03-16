<script setup lang="ts">
import {PropType} from "vue";
import {ButtonType} from "~/components/atoms/Button.vue";

defineProps({
  formTitle: {
    type: String,
    require: true,
    default: ''
  },
  button: {
    type: Object as PropType<ButtonType>,
    require: true
  },
  btnDisabled: {
    type: Boolean,
    require: true
  }
})
</script>

<template>
  <div class="py-8 px-4 mx-auto max-w-2xl lg:py-16">
    <h2 class="mb-12 text-xl font-bold text-gray-900 dark:text-white text-center">{{ formTitle }}</h2>
    <slot name="errorNoti" />
    <form @submit.prevent="$emit('form-submit')">
      <slot />
      <BtnWithSpinner :button="button" :disabled="btnDisabled">
        {{ button.text }}
      </BtnWithSpinner>
      <NuxtLink
          to="/dm/list"
          type="button"
          class="text-white bg-gray-800 hover:bg-gray-900 focus:outline-none focus:ring-4
            focus:ring-gray-300 font-medium rounded-lg text-sm px-5 py-2.5 mr-2 mb-2 dark:bg-gray-800
            dark:hover:bg-gray-700 dark:focus:ring-gray-700 dark:border-gray-700"
      >
        Cancel
      </NuxtLink>
    </form>
  </div>
</template>