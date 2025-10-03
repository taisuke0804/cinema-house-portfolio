<script setup lang="ts">
import { Link } from '@inertiajs/vue3'
import dayjs from 'dayjs' // JavaScriptの日付処理・操作のライブラリ
import { ref } from 'vue'

defineProps<{
  screeningsByDate: Record<
    string,
    {
      id: number
      start_time: string
      end_time: string
      movie: { id: number; title: string }
    }[]
  >
  routeName: string // リンク先のルート名 (admin / user で切り替える用)
}>()

const selectedDate = ref(new Date())
</script>

<template>
  <el-calendar v-model="selectedDate">
    <template #date-cell="{ data }">
      <div class="text-sm font-medium">
        {{ dayjs(data.day).format('D日') }}
      </div>

      <ul class="mt-1 space-y-1">
        <li
          v-for="s in screeningsByDate[data.day] || []"
          :key="s.id"
          class="truncate"
        >
          <el-tooltip effect="dark" placement="top">
            <template #content>
              タイトル：『{{ s.movie.title }}』<br />
              上映時間： {{ dayjs(s.start_time).format('HH:mm') }}
              ～ {{ dayjs(s.end_time).format('HH:mm') }}
            </template>

            <Link :href="route(routeName, s.id)">
              <span class="cursor-pointer text-xs text-blue-600 underline">
                {{ s.movie.title }}
              </span>
            </Link>
          </el-tooltip>
        </li>
      </ul>
    </template>
  </el-calendar>
</template>

<style scoped>
:deep(.el-calendar-table .el-calendar-day) {
  height: 150px;
}
</style>
