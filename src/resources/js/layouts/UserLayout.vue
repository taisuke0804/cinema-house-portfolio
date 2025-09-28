<script setup lang="ts">
import UserHeader from '@/components/user/UserHeader.vue'
</script>

<template>
  <div>
    <UserHeader />
    <main class="p-6">
      <slot />
    </main>
  </div>
</template>
