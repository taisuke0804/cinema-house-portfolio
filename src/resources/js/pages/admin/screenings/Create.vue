<script setup lang="ts">
import { Head, useForm } from '@inertiajs/vue3'
import AdminLayout from '@/layouts/AdminLayout.vue'
import type { FormInstance, FormRules } from 'element-plus'
import { ref } from 'vue'

defineOptions({
  layout: AdminLayout
})

const screeningFormRef = ref<FormInstance>()

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

const storeScreeningRules: FormRules<ScreeningForm> = {
  screening_date: [
    { required: true, message: '上映日付は必須項目です', trigger: 'blur' },
  ],
  start_time: [
    { required: true, message: '上映開始時間は必須項目です', trigger: 'blur' },
  ],
  end_time: [
    { required: true, message: '上映終了時間は必須項目です', trigger: 'blur' },
  ],
}

const confirmDialogVisible = ref(false)

const handleOpenConfirm = async(formEl: FormInstance | undefined) => {
  if (!formEl) return
  await formEl.validate((valid, fields) => {
    if (valid) {
      confirmDialogVisible.value = true
    }
  })
}

// モーダルの送信処理
const submitScreeningStore = () => {
  screeningForm.post(route('admin.screenings.store', props.movie.id), {
    onSuccess: () => {
      confirmDialogVisible.value = false
    },
    onError: () => {
      confirmDialogVisible.value = false // バックエンド側のバリデーションエラー発生時にモーダルを閉じる
    },
  })
}

</script>
<template>
  <Head title="上映スケジュール新規登録" />

  <div class="max-w-3xl mx-auto py-8">
    <h1 class="text-2xl font-bold mb-6">上映スケジュール新規登録</h1>

    <!-- バックエンド側のバリデーションエラーメッセージ -->
    <el-alert v-if="Object.keys(screeningForm.errors).length" title="入力に不備があります。下記をご確認ください。" type="error" show-icon :closable="false" >
      <ul class="text-sm text-red-700 list-disc list-inside">
        <li v-for="(message, field) in screeningForm.errors" :key="field">
          {{ message }}
        </li>
      </ul>
    </el-alert>
    
    <el-form 
      label-position="top" 
      class="space-y-6" 
      ref="screeningFormRef"
      :model="screeningForm"
      :rules="storeScreeningRules"
    >
      <el-form-item label="映画タイトル">
        <el-input :model-value="props.movie.title" disabled />
      </el-form-item>

      <el-form-item label="上映日付" prop="screening_date" >
        <el-date-picker
          v-model="screeningForm.screening_date"
          type="date"
          placeholder="年/月/日"
          style="width: 50%"
          format="YYYY/MM/DD"
          value-format="YYYY-MM-DD"
        />
      </el-form-item>

      <el-form-item label="上映開始時間" prop="start_time" >
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

      <el-form-item label="上映終了時間" prop="end_time" >
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
        <el-button 
          type="primary" 
          size="large" 
          @click="handleOpenConfirm(screeningFormRef)" 
          :loading="screeningForm.processing"
        >
          登録
        </el-button>
      </div>

      <!-- モーダル -->
      <el-dialog
        v-model="confirmDialogVisible"
        title="上映スケジュール登録確認"
        width="400px"
        :show-close="false"
      >
        <p>入力内容を送信してよろしいですか？</p>

        <template #footer>
          <el-button @click="confirmDialogVisible = false">キャンセル</el-button>
          <el-button type="primary" @click="submitScreeningStore" >送信</el-button>
        </template>

      </el-dialog>

    </el-form>
  </div>
</template>
<style scoped>
:deep(.el-alert) {
  margin-bottom: 8px;
}
</style>