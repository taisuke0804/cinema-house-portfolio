<script setup lang="ts">
import { Head, useForm, Link } from '@inertiajs/vue3'
import AdminLayout from '@/layouts/AdminLayout.vue'
import type { FormInstance, FormRules } from 'element-plus'
import { reactive, ref } from 'vue'

defineOptions({
  layout: AdminLayout
})

const userFormRef = ref<FormInstance>()

interface UserForm {
  name: string
  email: string
  password: string
  password_confirmation: string
}

const userForm = useForm<UserForm>({
  name: '',
  email: '',
  password: '',
  password_confirmation: '',
})

const validatePass = (rule: any, value: any, callback: any) => {
  if (value === '') {
    callback(new Error('パスワードを入力してください'))
  } else {
    if (userForm.password_confirmation !== '') {
      if (!userFormRef.value) return
      userFormRef.value.validateField('password_confirm')
    }
    callback()
  }
}

const validatePass2 = (rule: any, value: any, callback: any) => {
  if (value === '') {
    callback(new Error('パスワードをもう一度入力してください'))
  } else if (value !== userForm.password) {
    callback(new Error("パスワードが一致していません"))
  } else {
    callback()
  }
}

const storeUserRules = reactive<FormRules<typeof userForm>>({
  name: [
    { required: true, message: '名前は必須項目です', trigger: 'blur' },
  ],
  email: [
    { required: true, message: 'メールアドレスは必須項目です', trigger: 'blur' },
    { type: 'email', message: '有効なメールアドレスを入力してください', trigger: ['blur', 'change'] },
  ],
  password: [{ required: true, validator: validatePass, trigger: 'blur' }],
  password_confirmation: [{ required: true, validator: validatePass2, trigger: 'blur' }],
})

const confirmDialogVisible = ref(false)

const handleOpenConfirm = async(formEl: FormInstance | undefined) => {
  if (!formEl) return
  await formEl.validate((valid, fields) => {
    if (valid) {
      confirmDialogVisible.value = true
    }
  })
}

const submitUserStore = () => {
  userForm.post(route('admin.users.store'), {
    onSuccess: () => {
      confirmDialogVisible.value = false
      userForm.reset()
    },
    onError: () => {
      confirmDialogVisible.value = false // バックエンド側のバリデーションエラー発生時にモーダルを閉じる
    },
  })
}

</script>
<template>
  <Head title="ユーザー新規登録" />

  <div class="max-w-2xl mx-auto p-6 bg-gray-50 rounded-lg shadow-sm">
    <h1 class="text-2xl font-bold mb-8">ユーザー新規登録</h1>

    <el-alert v-if="Object.keys(userForm.errors).length" title="入力に不備があります。下記をご確認ください。" type="error" show-icon :closable="false" >
      <ul class="text-sm text-red-700 list-disc list-inside">
        <li v-for="(message, field) in userForm.errors" :key="field">
          {{ message }}
        </li>
      </ul>
    </el-alert>

    <el-form 
      label-position="top" 
      @submit.prevent="handleOpenConfirm"
      ref="userFormRef"
      :model="userForm" 
      :rules="storeUserRules"
    >

      <el-form-item label="名前" prop="name" >
        <el-input v-model="userForm.name" placeholder="名前を入力" />
      </el-form-item>

      <el-form-item label="メールアドレス" prop="email" >
        <el-input v-model="userForm.email" placeholder="example@example.com" />
      </el-form-item>

      <el-form-item label="パスワード" prop="password">
        <el-input
          v-model="userForm.password"
          type="password"
          show-password
          placeholder="8文字以上で入力"
        />
      </el-form-item>

      <el-form-item label="パスワード確認" prop="password_confirmation">
        <el-input
          v-model="userForm.password_confirmation"
          type="password"
          show-password
          placeholder="もう一度入力"
        />
      </el-form-item>

      <div class="flex justify-between mt-8">
        <Link
          :href="route('admin.users.index')"
          class="el-button el-button--default"
        >
          戻る
        </Link>

        <el-button
          type="primary"
          :loading="userForm.processing"
          @click="handleOpenConfirm(userFormRef)"
        >
          登録
        </el-button>
      </div>

      <!-- モーダル -->
      <el-dialog
        v-model="confirmDialogVisible"
        title="ユーザー新規登録"
        width="400px"
        :show-close="false"
      >
        <p>入力内容を送信してよろしいですか？</p>

        <template #footer>
          <el-button @click="confirmDialogVisible = false">キャンセル</el-button>
          <el-button type="primary" @click="submitUserStore" >送信</el-button>
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