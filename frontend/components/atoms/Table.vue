<script setup lang="ts">
</script>

<template>
  <div class="table-wrap">
    <table class="w-full text-sm text-left text-gray-500 dark:text-gray-400">
      <slot name="table-header" />
      <slot name="table-body" />
    </table>
  </div>
</template>