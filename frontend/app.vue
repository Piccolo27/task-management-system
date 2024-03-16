<template>
  <div>
    <NuxtLayout>
      <NuxtPage />
    </NuxtLayout>
  </div>
</template>

<script setup lang="ts">
import { initFlowbite } from 'flowbite';

const appConfig = useAppConfig();

onMounted(() => {
  initFlowbite()
})

useHead({
  title: appConfig.title
})
</script>