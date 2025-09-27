<script setup lang="ts">
import AdminLayout from '@/layouts/AdminLayout.vue'
import { Head, Link } from '@inertiajs/vue3'
import { ref, computed } from 'vue'
import dayjs from 'dayjs' // JavaScriptの日付処理・操作のライブラリ

defineOptions({
  layout: AdminLayout
})

const selectedDate = ref(new Date()) // 現在の日付と時刻を取得

const props = defineProps<{
  screenings: {
    id: number
    start_time: string
    end_time: string
    movie: { id: number; title: string }
  }[]
}>()

// 日付文字列ごとに上映スケジュールをまとめる
const screeningsByDate = computed(() => {
  const map: { [key: string]: typeof props.screenings } = {}
  props.screenings.forEach(sc => {
    const dateKey = dayjs(sc.start_time).format('YYYY-MM-DD')
    if (!map[dateKey]) map[dateKey] = []
    map[dateKey].push(sc)
  })
  return map
})

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

    <el-calendar v-model="selectedDate">
      <template #date-cell="{ data }" >
        <!-- 日付 -->
        <div class="text-sm font-medium">
          {{ dayjs(data.day).format('D日') }}
        </div>

        <!-- 上映スケジュール -->
        <ul class="mt-1 space-y-1">
          <li
            v-for="s in screeningsByDate[data.day] || []"
            :key="s.id"
            class="truncate"
          >
            <el-tooltip
              effect="dark"
              placement="top"
            >

              <template #content>
                タイトル： 『{{ s.movie.title }}』<br />
                上映時間： {{ dayjs(s.start_time).format('HH:mm') }} ～ {{ dayjs(s.end_time).format('HH:mm') }}
              </template>

              <Link :href="route('admin.screenings.show', s.id)">
                <span class="cursor-pointer text-xs text-blue-600 underline">
                  {{ s.movie.title }}
                </span>
              </Link>

            </el-tooltip>
          </li>
        </ul>

      </template>
    </el-calendar>
  </div>
</template>
<style scoped>
/* カレンダーセルの日付枠サイズを拡張 */
:deep(.el-calendar-table .el-calendar-day) {
  height: 150px; /* デフォルトは60px前後 */
}
</style>