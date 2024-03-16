<script setup lang="ts">
defineProps(['button', 'disabled'])
</script>

<template>
  <Button :button="button" :disabled="disabled">
    <slot />
    <Spinner v-if="disabled" />
  </Button>
</template>

<style scoped>

</style>