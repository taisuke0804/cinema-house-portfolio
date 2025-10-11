<script setup lang="ts">
import { Head, useForm  } from '@inertiajs/vue3'
import AdminLayout from '@/layouts/AdminLayout.vue'
import { ref } from 'vue'
import type { FormInstance, FormRules, ElMessage } from 'element-plus'

// レイアウトを適用
defineOptions({
  layout: AdminLayout
})

const sendNotificationFormRef = ref<FormInstance>()

interface NotificationForm {
  subject: string
  bodyText: string
}

// Inertia フォーム
const NotificationForm = useForm<NotificationForm>({
  subject: '',
  bodyText: '',
})

const notificationRules: FormRules<NotificationForm> = {
  subject: [
    { required: true, message: '件名は必須項目です', trigger: 'blur' },
  ],
  bodyText: [
    { required: true, message: '本文は必須項目です', trigger: 'blur' },
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

const submitNotification = () => {
  NotificationForm.post(route('admin.notifications.send'), {
    onSuccess: () => {
      confirmDialogVisible.value = false
      NotificationForm.reset()
      sendNotificationFormRef.value?.clearValidate()
    },
    onError: () => {
      confirmDialogVisible.value = false // バックエンド側のバリデーションエラー発生時にモーダルを閉じる
    },
  })
}

</script>
<template>
  <Head title="管理者ダッシュボード" />

  <main class="max-w-4xl mx-auto py-12 px-4">
    <h1 class="text-3xl font-bold mb-4">管理者ダッシュボード</h1>

    <p class="text-lg mb-6 text-gray-700">ここは管理者トップページです。</p>

    <el-alert
      v-if="$page.props.flash.success"
      :title="$page.props.flash.success"
      type="success"
      show-icon
      closable
    />

    <el-card class="max-w-3xl">
      <template #header>
        <div class="font-semibold">ユーザーへの一斉通知メール送信</div>
      </template>

      <el-form 
        ref="sendNotificationFormRef"
        :model="NotificationForm"
        :rules="notificationRules"
        @submit.prevent="handleOpenConfirm(sendNotificationFormRef)" 
        label-position="top"
        class="space-y-4"
      >
        <el-alert v-if="Object.keys(NotificationForm.errors).length" title="入力に不備があります。下記をご確認ください。" type="error" show-icon
          :closable="false">
          <ul class=" text-sm text-red-700 list-disc list-inside">
            <li v-for="(message, field) in NotificationForm.errors" :key="field">
              {{ message }}
            </li>
          </ul>
        </el-alert>

        <div>
          <el-form-item label="件名" prop="subject" >
            <el-input
              v-model="NotificationForm.subject"
              placeholder="（例）新作映画のお知らせ"
            />
          </el-form-item>
        </div>
        <div>
          <el-form-item label="本文" prop="bodyText" >
            <el-input
              v-model="NotificationForm.bodyText"
              type="textarea"
              :rows="6"
              placeholder="（例）いつもCINEMA-HOUSEをご利用いただきありがとうございます。今週末より新作映画を公開予定です。ぜひご予約ください。"
            />
          </el-form-item>
          <p class="text-gray-400 text-xs">
            ※この文面はポートフォリオ用のサンプルです。実際の通知内容ではありません。
          </p>
        </div>

        <div class="flex justify-start">
          <el-button
            type="primary"
            native-type="submit"
            :loading="NotificationForm.processing"
          >
            一斉送信
          </el-button>
        </div>

        <!-- モーダル -->
        <el-dialog
        v-model="confirmDialogVisible"
        title="一斉通知メール送信"
        width="400px"
        :show-close="false"
      >
        <p>ユーザーにメールを一斉送信してよろしいですか？</p>

        <template #footer>
          <el-button @click="confirmDialogVisible = false">キャンセル</el-button>
          <el-button type="primary" @click="submitNotification" >送信</el-button>
        </template>

      </el-dialog>

      </el-form>
    </el-card>
  </main>
</template>
<style scoped>
:deep(.el-alert) {
  margin-bottom: 8px;
}

:deep(label) {
  font-weight: 700;
}
</style>