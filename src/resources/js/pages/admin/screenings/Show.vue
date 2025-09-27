<script setup lang="ts">
import { Head } from '@inertiajs/vue3'
import AdminLayout from '@/layouts/AdminLayout.vue'

defineOptions({
  layout: AdminLayout
})

// Laravel側から渡されるprops
const props = defineProps<{
  screening: {
    screening_id: number
    screening_date: string
    start_time: string
    end_time: string
    movie: {
      title: string
      genre: string
      genre_label: string
    }
    seats: Record<string, {
      id: number
      row: string
      number: number
      is_reserved: boolean
      user_id?: number | null
    }[]>
  }
}>()

</script>
<template>
  <Head title="上映スケジュール詳細" />

  <div class="max-w-6xl mx-auto py-10">
    <el-card shadow="always" class="mb-6" header-class="bg-blue-600 text-white" >
      <template #header >
        <span class="font-semibold">上映スケジュール詳細</span>
      </template>

      <div class="space-y-1 mb-3">
        <p class="text-lg font-semibold">
          <strong class="mr-2">映画タイトル:</strong>
           『{{ props.screening.movie.title }}』
        </p>

        <p class="text-base text-gray-600">
          <strong class="mr-2">ジャンル:</strong>
            {{ props.screening.movie.genre_label }}
        </p>
        <p class="text-base text-gray-600">
          <strong class="mr-2">上映日:</strong>
            {{ props.screening.screening_date }}
        </p>
        <p class="text-base text-gray-600">
          <strong class="mr-2">上映開始:</strong>
            {{ props.screening.start_time }}
        </p>
        <p class="text-base text-gray-600">
          <strong class="mr-2">上映終了:</strong>
            {{ props.screening.end_time }}
        </p>
      </div>

      <div class="border-t-1 border-gray-300"></div>

      <!-- 凡例 -->
      <h3 class="my-2 font-bold">座席予約状況</h3>
      <p class="text-sm text-gray-500 mb-3">
        緑色: 空席 / 灰色: 予約済み
      </p>

      <!-- 座席表 -->
      <div v-for="(rowSeats, row) in props.screening.seats" :key="row" class="flex items-center mb-3">
        <span class="w-6 font-bold">{{ row }}</span>

        <div class="grid grid-cols-10 gap-3">
          <div
            v-for="seat in rowSeats"
            :key="seat.id"
            class="w-10 h-10 flex items-center justify-center rounded text-white text-sm"
            :class="{
              'bg-green-600': !seat.is_reserved,
              'bg-gray-400': seat.is_reserved
            }"
          >
            {{ seat.number }}
          </div>
        </div>
      </div>

    </el-card>
  </div>
  
</template>