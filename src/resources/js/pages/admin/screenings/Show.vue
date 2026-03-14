<script setup lang="ts">
import { Head } from '@inertiajs/vue3'
import AdminLayout from '@/layouts/AdminLayout.vue'
import { computed } from 'vue'
import { onMounted, ref } from 'vue'
import axios from 'axios'

defineOptions({
  layout: AdminLayout
})

// Laravel側から渡されるprops
const props = defineProps<{
  screening: {
    screening_id: number
    date: string
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

type SeatApiResponse = {
  screening_id: number
  total_seats: number
  reserved_seats: number
  available_seats: number
  occupancy_rate: number
  seats: {
    id: number
    row: string
    number: number
    label: string
    is_reserved: boolean
  }[]
}

const seatSummary = ref({
  total_seats: 0,
  reserved_seats: 0,
  available_seats: 0,
  occupancy_rate: 0,
})

const isLoadingSeatSummary = ref(false)

const fetchSeatSummary = async () => {
  isLoadingSeatSummary.value = true

  try {
    const { data } = await axios.get<SeatApiResponse>(
      route('admin.api.screenings.seats.index', {
        screening: props.screening.screening_id,
      }),
    )

    seatSummary.value = {
      total_seats: data.total_seats,
      reserved_seats: data.reserved_seats,
      available_seats: data.available_seats,
      occupancy_rate: data.occupancy_rate,
    }

  } catch (error) {
    console.error('座席集計情報の取得に失敗しました。', error)
  } finally {
    isLoadingSeatSummary.value = false
  }
}

onMounted(() => {
  fetchSeatSummary()
})

// 上映日が過去かどうか
const isPastScreening = computed(() => {
  const today = new Date()
  today.setHours(0, 0, 0, 0) // 時間は無視して日付だけ比較
  const screeningDate = new Date(props.screening.date)
  return screeningDate < today
})

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

      <section class="mt-3 border-t pt-2 border-gray-300">
        <h2 class="text-xl font-bold mb-4">
          座席集計情報
        </h2>

        <div v-if="isLoadingSeatSummary" class="text-gray-500">
          集計情報を読み込み中です...
        </div>

        <div v-else class="grid grid-cols-2 md:grid-cols-4 gap-4">
          <div class="bg-white border rounded-md p-4 shadow-sm">
            <p class="text-sm text-gray-500">総座席数</p>
            <p class="text-2xl font-bold">{{ seatSummary.total_seats }}</p>
          </div>

          <div class="bg-white border rounded-md p-4 shadow-sm">
            <p class="text-sm text-gray-500">予約済み</p>
            <p class="text-2xl font-bold text-gray-700">{{ seatSummary.reserved_seats }}</p>
          </div>

          <div class="bg-white border rounded-md p-4 shadow-sm">
            <p class="text-sm text-gray-500">空席数</p>
            <p class="text-2xl font-bold text-green-600">{{ seatSummary.available_seats }}</p>
          </div>

          <div class="bg-white border rounded-md p-4 shadow-sm">
            <p class="text-sm text-gray-500">利用率</p>
            <p class="text-2xl font-bold text-blue-600">{{ seatSummary.occupancy_rate }}%</p>
          </div>

        </div>
      </section>

      <div class="border-t-1 border-gray-300 mt-3"></div>

      <template v-if="isPastScreening" >
        <div class="bg-red-300 p-4 my-2.5 rounded-lg">
          <p>この上映スケジュールの予約は終了しました。</p>
        </div>
      </template>
      <template v-else>

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

      </template>

    </el-card>
  </div>

</template>
