<script setup lang="ts">
import UserLayout from '@/layouts/UserLayout.vue'
import { Head, router } from '@inertiajs/vue3'
import { ElMessage, ElMessageBox } from 'element-plus'


defineOptions({
  layout: UserLayout
})

defineProps<{
  reservations: {
    id: number
    row: string
    number: number
    screening: {
      start_format: string
      end_format: string
      date: string
      movie: {
        title: string
      }
    }
  }[]
}>()

/**
 * キャンセル確認 → 実行
 */
const confirmCancel = (seat_id: number) => {
  ElMessageBox.confirm(
    'この予約をキャンセルしますか？',
    '確認',
    {
      confirmButtonText: 'キャンセルする',
      cancelButtonText: '戻る',
      type: 'warning',
    }
  )
    .then(() => {
      router.delete(route('user.reservations.cancel', seat_id))
    })
    .catch(() => {
      // キャンセル時は何もしない
    })
}

</script>
<template>
  <Head title="座席予約一覧" />
  
  <div class="p-12">
    <h1 class="text-2xl font-bold mb-6">座席予約一覧</h1>

    <el-alert
      v-if="$page.props.flash.success"
      :title="$page.props.flash.success"
      type="success"
      show-icon
      closable
    />

    <div class="mb-6">
      <div class="bg-blue-100 text-blue-800 px-4 py-3 rounded mb-6">
        当日はPDF画面を提示していただき、ご入場ください。
      </div>

      <div v-if="reservations.length" class="overflow-x-auto bg-white shadow rounded">
        <table class="w-full border-collapse text-left">
          <thead class="bg-gray-100">
            <tr>
              <th class="px-4 py-2 border">映画タイトル</th>
              <th class="px-4 py-2 border">上映日</th>
              <th class="px-4 py-2 border">上映時間</th>
              <th class="px-4 py-2 border">座席番号</th>
              <th class="px-4 py-2 border">操作</th>
            </tr>
          </thead>

          <tbody>
            <tr v-for="reservation in reservations" :key="reservation.id" class="hover:bg-gray-50">
              <td class="px-4 py-2 border">
                {{ reservation.screening.movie.title }}
              </td>
              <td class="px-4 py-2 border">
                {{ reservation.screening.date }}
              </td>
              <td class="px-4 py-2 border">
                {{ reservation.screening.start_format }} ～ {{ reservation.screening.end_format }}
              </td>
              <td class="px-4 py-2 border">
                {{ reservation.row }}{{ reservation.number }}
              </td>
              <td class="px-4 py-2 border space-x-4">
                <a
                  :href="route('user.reservations.pdf', reservation.id)"
                  target="_blank"
                  class="bg-blue-500 text-white px-3 py-1 rounded hover:bg-blue-600"
                >
                  PDF出力
                </a>
                <button 
                  class="bg-red-500 text-white px-3 py-1 rounded hover:bg-red-600 cursor-pointer"
                  @click="confirmCancel(reservation.id)"
                >
                  キャンセル
                </button>
              </td>
            </tr>
          </tbody>
        </table>
      </div>

      <div v-else class="bg-blue-100 text-blue-800 px-4 py-3 rounded mb-6">
        現在、予約されている座席はありません。
      </div>

    </div>

  </div>
</template>
<style scoped>
:deep(.el-alert) {
  margin-bottom: 12px;
}
</style>