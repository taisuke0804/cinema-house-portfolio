<script setup lang="ts">
import { Head, usePage, useForm } from '@inertiajs/vue3'
import UserLayout from '@/layouts/UserLayout.vue'
import { ref, computed, watch } from 'vue'

defineOptions({
  layout: UserLayout
})

const page = usePage()

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
  },
  authReservedSeat: {
    row: string
    number: number
  } | null
}>()

const reserveForm = useForm({
  screening_id: props.screening.screening_id,
  seat_id: null as number | null,
  row: '' as string,
  number: null as number | null,
})

// 追加: 表示用の "A1" 形式ラベル
const seatLabel = ref<string | null>(null)

const selectedSeatId = ref<number | null>(null)

watch(selectedSeatId, (v) => { reserveForm.seat_id = v }, { immediate: true })

const toggleSeat = (seat: { id: number; is_reserved: boolean; user_id?: number | null; row: string; number: number }) => {
  // 1人1席のみの仕様: 既に予約済みなら選択不可
  if (props.authReservedSeat || seat.is_reserved ) return

  selectedSeatId.value = (selectedSeatId.value === seat.id) ? null : seat.id

  seatLabel.value = `${seat.row}${seat.number}`

  reserveForm.seat_id = selectedSeatId.value
  reserveForm.row = seat.row
  reserveForm.number = seat.number
}

const confirmDialogVisible = ref(false)

// モーダル
const handleOpenConfirm = () => {
  if (reserveForm.seat_id == null) return
  confirmDialogVisible.value = true
}

// 追加: 予約POST
const reserveSeat = () => {
  if (reserveForm.seat_id == null) return
  reserveForm.post(route('user.seats.reserve'), {
    onSuccess: () => {
      confirmDialogVisible.value = false
    }
  })
}

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

      <div v-if="props.authReservedSeat" class="bg-yellow-200 p-4 my-2.5 rounded-lg">
        <p>あなたの予約済み座席: {{ props.authReservedSeat.row }}{{ props.authReservedSeat.number }}</p>
      </div>

      <!-- バックエンド側のバリデーションエラーメッセージ -->
      <el-alert v-if="Object.keys(reserveForm.errors).length" title="入力に不備があります。下記をご確認ください。" type="error" show-icon :closable="false" >
        <ul class="text-sm text-red-700 list-disc list-inside">
          <li v-for="(message, field) in reserveForm.errors" :key="field">
            {{ message }}
          </li>
        </ul>
      </el-alert>

      <!-- 凡例 -->
      <h3 class="my-2 font-bold">座席予約状況</h3>
      <p class="text-sm text-gray-500 mb-3">
        緑色: 空席 / 灰色: 予約済み / 黄色: 自分の予約 / 青色: 選択中
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
              'bg-blue-600': selectedSeatId === seat.id,
              'bg-green-600': !seat.is_reserved && selectedSeatId !== seat.id, 
              'bg-gray-400': seat.is_reserved && seat.user_id !== page.props.auth.user?.id,
              'bg-yellow-400': props.authReservedSeat 
                 && seat.row === props.authReservedSeat.row 
                 && seat.number === props.authReservedSeat.number,
              'cursor-pointer': !seat.is_reserved && !props.authReservedSeat
            }"
            @click="toggleSeat(seat)"
          >
            {{ seat.number }}
          </div>
        </div>
      </div>

      <template v-if="!props.authReservedSeat">
        <div class="border-t-1 border-gray-300"></div>
  
        <h3 class=" font-bold leading-none my-3">選択した座席情報</h3>
  
        <p class="mb-4" v-if="selectedSeatId != null">
          選択した座席: {{ seatLabel }}
        </p>
        <p class="mb-4" v-else>座席が選択されていません。</p>
  
        <el-button
          type="primary"
          :disabled="selectedSeatId == null || reserveForm.processing"
          :loading="reserveForm.processing"
          @click="handleOpenConfirm" 
        >
          予約する
        </el-button>
  
        <!-- モーダル -->
        <el-dialog
          v-model="confirmDialogVisible"
          title="座席予約確認"
          width="400px"
          :show-close="false"
        >
          <p>入力内容を送信してよろしいですか？</p>
  
          <template #footer>
            <el-button @click="confirmDialogVisible = false">キャンセル</el-button>
            <el-button type="primary" @click="reserveSeat" >送信</el-button>
          </template>
        </el-dialog>
      </template>

    </el-card>
  </div>
</template>
<style scoped>
:deep(.el-alert) {
  margin-top: 8px;
}
</style>