<script setup lang="ts">
import { Head, Link, useForm } from '@inertiajs/vue3'
import { ref } from 'vue'
import type { FormInstance, FormRules } from 'element-plus'

const adminLoginFormRef = ref<FormInstance>()

interface AdminLoginForm {
  email: string
  password: string
}
// InertiaのuseFormフックを使ってフォームデータを作成
const AdminLoginForm = useForm<AdminLoginForm>({
  email: '',
  password: '',
})

// ElementPlus用のバリデーションルールを型付きで定義
const adminLoginRules: FormRules<AdminLoginForm> = {
  email: [
    { required: true, message: 'メールアドレスは必須項目です', trigger: 'blur' },
  ],
  password: [
    { required: true, message: 'パスワードは必須項目です', trigger: 'blur' },
  ],
}

const submitAdminLogin = async(formEl: FormInstance | undefined) => {
  if (!formEl) return
  await formEl.validate((valid, fields) => {
    if (valid) {
      AdminLoginForm.post(route('admin.login.attempt'), {
        preserveState: true, //ページコンポーネントを再描画 入力値を保持
        onFinish: () => {
          AdminLoginForm.password = '' //リクエスト終了時にパスワード欄をリセット
        },
        preserveScroll: true, //スクロール位置を維持
      })
    }
  })
}

</script>

<template>
  <Head title="管理者ログイン" />
  <main class="max-w-md mx-auto py-12">

    <el-card style="max-width: 520px" header-class="bg-blue-500 text-white">
      <template #header>
        <h1 class="text-2xl font-semibold">管理者ログイン</h1>
      </template>
  
      <!-- @submit.prevent ENTERキーで送信 -->
      <el-form 
        ref="adminLoginFormRef" 
        :model="AdminLoginForm" 
        :rules="adminLoginRules" 
        label-position="top" 
        @submit.prevent="submitAdminLogin(adminLoginFormRef)"
      >
        <el-form-item label="メールアドレス" prop="email" >
          <el-input v-model="AdminLoginForm.email" placeholder="メールアドレスを入力" autocomplete="username" />
        </el-form-item>
  
        <el-form-item label="パスワード" prop="password">
          <el-input type="password" v-model="AdminLoginForm.password" show-password placeholder="パスワードを入力" autocomplete="off" />
        </el-form-item>

        <el-alert v-if="Object.keys(AdminLoginForm.errors).length" title="入力に不備があります。下記をご確認ください。" type="error" show-icon
          :closable="false">
          <ul class="mt-2 text-sm text-red-700 list-disc list-inside">
            <li v-for="(message, field) in AdminLoginForm.errors" :key="field">
              {{ message }}
            </li>
          </ul>
        </el-alert>

        <!-- AdminLoginForm.processing 送信中かどうか -->
        <el-button 
          type="primary" 
          @click="submitAdminLogin(adminLoginFormRef)" 
          class="w-full mt-5"
          native-type="submit"
          :loading="AdminLoginForm.processing"
        >
          ログイン
        </el-button>
  
        <div class="mt-4">
          <Link href="/" class="text-sm text-blue-600 hover:underline">トップに戻る</Link>
        </div>
      </el-form>
    </el-card>
  </main>
</template>
