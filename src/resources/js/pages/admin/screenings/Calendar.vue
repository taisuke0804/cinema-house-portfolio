<script setup lang="ts">
import AdminLayout from '@/layouts/AdminLayout.vue'
import { Head } from '@inertiajs/vue3'
import ScreeningCalendar from '@/components/ScreeningCalendar.vue'
import type { Screening } from '@/types/screening'

defineOptions({
  layout: AdminLayout
})

defineProps<{
  screeningsByDate: Record<string, Screening[]>
}>()

</script>
<template>
  <Head title="上映カレンダー" />

  <div class="p-6">
    <h1 class="text-2xl font-bold mb-4">上映スケジュールカレンダー</h1>

    <!-- フラッシュメッセージ -->
    <el-alert
      v-if="$page.props.flash.success"
      :title="$page.props.flash.success"
      type="success"
      show-icon
      closable
    />

    <!-- カレンダーコンポーネント -->
    <ScreeningCalendar :screeningsByDate="screeningsByDate" routeName="admin.screenings.show" />
    
  </div>
</template>