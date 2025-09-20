<script setup lang="ts">
import { Head } from '@inertiajs/vue3'
import AdminLayout from '@/layouts/AdminLayout.vue'

defineOptions({
  layout: AdminLayout
})

const props = defineProps<{
  movies: {
    data: { id: number; title: string; genre: number; description: string }[],
    current_page: number,
    last_page: number,
    links: { url: string | null; label: string; active: boolean }[],
  }
}>()

</script>
<template>
  <Head title="映画一覧" />
  映画一覧
</template>