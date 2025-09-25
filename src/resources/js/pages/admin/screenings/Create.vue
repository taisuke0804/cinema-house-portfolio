<script setup lang="ts">
import { Head, useForm } from '@inertiajs/vue3'
import AdminLayout from '@/layouts/AdminLayout.vue'

defineOptions({
  layout: AdminLayout
})

const props = defineProps<{
  movie: {
    id: number
    title: string
  }
}>()

interface ScreeningForm {
  movie_id: number
  screening_date: string
  start_time: string
  end_time: string
}

const screeningForm = useForm<ScreeningForm>({
  movie_id: props.movie.id,
  screening_date: '',
  start_time: '',
  end_time: '',
})

const submit = () => {
  console.log('confirm')
}

</script>
<template>
  <Head title="上映スケジュール新規登録" />

  <div class="max-w-3xl mx-auto py-8">
    <h1 class="text-2xl font-bold mb-6">上映スケジュール新規登録</h1>

    <el-form label-position="top" class="space-y-6" @submit.prevent="submit">
      <el-form-item label="映画タイトル">
        <el-input :model-value="props.movie.title" disabled />
      </el-form-item>

      <el-form-item label="上映日付">
        <el-date-picker
          v-model="screeningForm.screening_date"
          type="date"
          placeholder="年/月/日"
          style="width: 50%"
          format="YYYY/MM/DD"
          value-format="YYYY-MM-DD"
        />
      </el-form-item>

      <el-form-item label="上映開始時間">
        <div class="flex gap-2 w-1/2">
          <el-time-select
            v-model="screeningForm.start_time"
            start="09:00"
            step="00:30"
            end="21:00"
            placeholder="時刻を選択"
            style="flex: 1"
          />
        </div>
      </el-form-item>

      <el-form-item label="上映終了時間">
        <div class="flex gap-2 w-1/2">
          <el-time-select
            v-model="screeningForm.end_time"
            start="09:00"
            step="00:30"
            end="23:30"
            placeholder="時刻を選択"
            style="flex: 1"
          />
        </div>
      </el-form-item>

      <div class="flex justify-start mt-8">
        <el-button type="primary" size="large" @click="submit" :loading="screeningForm.processing">
          登録
        </el-button>
      </div>
    </el-form>
  </div>
</template>