<script setup lang="ts">
import VueDatePicker from '@vuepic/vue-datepicker';
import '@vuepic/vue-datepicker/dist/main.css'

const date = ref<Date|null>(null)

const emit = defineEmits(['changeDateTime'])
const props = defineProps(['enableTimePicker'])

const enableTimePicker = ref(props.enableTimePicker)

watch(date, () => {
  emit('changeDateTime', date.value)
})
</script>

<template>
  <VueDatePicker v-model="date" dark :enable-time-picker="enableTimePicker"></VueDatePicker>
</template>

<style scoped>
.dp__theme_dark {
    --dp-background-color: rgb(55 65 81);
    --dp-text-color: #fff;
    --dp-hover-color: #484848;
    --dp-hover-text-color: #fff;
    --dp-hover-icon-color: #959595;
    --dp-primary-color: #005cb2;
    --dp-primary-disabled-color: #61a8ea;
    --dp-primary-text-color: #fff;
    --dp-secondary-color: #a9a9a9;
    --dp-border-color: rgb(75 85 99);
    --dp-menu-border-color: #2d2d2d;
    --dp-border-color-hover: rgb(59 130 246);
    --dp-disabled-color: #737373;
    --dp-disabled-color-text: #d0d0d0;
    --dp-scroll-bar-background: #212121;
    --dp-scroll-bar-color: #484848;
    --dp-success-color: #00701a;
    --dp-success-color-disabled: #428f59;
    --dp-icon-color: #ffffff;
    --dp-danger-color: #e53935;
    --dp-marker-color: #e53935;
    --dp-tooltip-color: #3e3e3e;
    --dp-highlight-color: rgb(0 92 178 / 20%);
    --dp-range-between-dates-background-color: var(--dp-hover-color, #484848);
    --dp-range-between-dates-text-color: var(--dp-hover-text-color, #fff);
    --dp-range-between-border-color: var(--dp-hover-color, #fff);
}
</style>