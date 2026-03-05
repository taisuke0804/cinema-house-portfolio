<script setup lang="ts">
import { Head, usePage, useForm, Link } from '@inertiajs/vue3'
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
    date: string
    screening_date: string
    start_time: string
    end_time: string
    movie: {
      id: number
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
  authReservedSeats: Array<{
    row: string
    number: number
  }>
}>()

// 上映日が過去かどうか
const isPastScreening = computed(() => {
  const today = new Date()
  today.setHours(0, 0, 0, 0) // 時間は無視して日付だけ比較
  const screeningDate = new Date(props.screening.date)
  return screeningDate < today
})

const reserveForm = useForm({
  screening_id: props.screening.screening_id,
  seat_ids: [] as number[],
})

const MAX_SELECT_SEATS = 3

const selectedSeats = ref<Array<{ id: number; row: string; number: number }>>([])

const selectedSeatLabels = computed(() => {
  return selectedSeats.value.map(s => `${s.row}${s.number}`)
})

// 選択状態 → フォームへ同期
watch(
  selectedSeats,
  () => {
    reserveForm.seat_ids = selectedSeats.value.map(s => s.id)
  },
  { deep: true, immediate: true }
)

const toggleSeat = (seat: { id: number; is_reserved: boolean; user_id?: number | null; row: string; number: number }) => {
  // 既に予約済みなら選択不可
  if (props.authReservedSeats.length > 0 || seat.is_reserved) return

  const idx = selectedSeats.value.findIndex(s => s.id === seat.id)

  // 既に選択済みなら解除
  if (idx !== -1) {
    selectedSeats.value.splice(idx, 1)
    return
  }

  // 最大3席まで
  if (selectedSeats.value.length >= MAX_SELECT_SEATS) return

  selectedSeats.value.push({ id: seat.id, row: seat.row, number: seat.number })
}

const confirmDialogVisible = ref(false)

const handleOpenConfirm = () => {
  if (reserveForm.seat_ids.length === 0) return
  confirmDialogVisible.value = true
}

const reserveSeat = () => {
  if (reserveForm.seat_ids.length === 0) return

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
            <Link :href="route('user.movies.show', props.screening.movie.id)" class="text-blue-600 hover:underline">
              『{{ props.screening.movie.title }}』
            </Link>
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

      <template v-if="isPastScreening" >
        <div class="bg-red-300 p-4 my-2.5 rounded-lg">
          <p>この上映スケジュールの予約は終了しました。</p>
        </div>
      </template>
      <template v-else>

        <div v-if="props.authReservedSeats.length > 0" class="bg-yellow-200 p-4 my-2.5 rounded-lg">
          <p>
            あなたの予約済み座席:
            {{ props.authReservedSeats.map(s => `${s.row}${s.number}`).join(', ') }}
          </p>
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
                'bg-blue-600': selectedSeats.some(s => s.id === seat.id),
                'bg-green-600': !seat.is_reserved && !selectedSeats.some(s => s.id === seat.id),

                // 他ユーザーの予約
                'bg-gray-400': seat.is_reserved
                    && seat.user_id !== page.props.auth.user?.id
                    && !props.authReservedSeats.some(
                        s => s.row === seat.row && s.number === seat.number
                        ),

                // 自分の予約済み席（複数対応）
                'bg-yellow-400': props.authReservedSeats.some(s => s.row === seat.row && s.number === seat.number),

                // 追加予約なし：自分が1席でも予約していたら選択できない
                'cursor-pointer':
                  !seat.is_reserved &&
                  props.authReservedSeats.length === 0 &&
                  !selectedSeats.some(s => s.id === seat.id)
              }"
              @click="toggleSeat(seat)"
            >
              {{ seat.number }}
            </div>
          </div>
        </div>

        <template v-if="props.authReservedSeats.length === 0">
          <div class="border-t-1 border-gray-300"></div>

          <h3 class=" font-bold leading-none my-3">選択した座席情報</h3>

          <p class="mb-4" v-if="selectedSeats.length">
            選択した座席: {{ selectedSeatLabels.join(', ') }}（{{ selectedSeats.length }} / 3）
          </p>
          <p class="mb-4" v-else>座席が選択されていません。</p>

          <el-button
            type="primary"
            :disabled="selectedSeats.length === 0 || reserveForm.processing"
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

      </template>


    </el-card>
  </div>
</template>
<style scoped>
:deep(.el-alert) {
  margin-top: 8px;
}
</style>
