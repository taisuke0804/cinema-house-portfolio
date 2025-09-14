<script setup lang="ts">
import AdminHeader from '@/components/admin/AdminHeader.vue'
</script>
<template>
  <div>
    <AdminHeader />
    <main class="p-6">
      <slot />
    </main>
  </div>
</template>