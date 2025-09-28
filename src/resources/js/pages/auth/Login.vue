<script setup lang="ts">
import { Head, Link, useForm } from '@inertiajs/vue3'
import type { FormInstance, FormRules } from 'element-plus'
import { ref } from 'vue'

interface UserLoginForm {
  email: string
  password: string
}

const UserLoginForm = useForm<UserLoginForm>({
  email: '',
  password: '',
})

const userLoginFormRef = ref<FormInstance>()

const userLoginRules: FormRules<UserLoginForm> = {
  email: [
    { required: true, message: 'メールアドレスは必須項目です', trigger: 'blur' },
  ],
  password: [
    { required: true, message: 'パスワードは必須項目です', trigger: 'blur' },
  ],
}

const submitUserLogin = async(formEl: FormInstance | undefined) => {
  if (!formEl) return
  await formEl.validate((valid, fields) => {
    if (valid) {
      UserLoginForm.post(route('user.login'), {
        preserveState: true, //ページコンポーネントを再描画 入力値を保持
        onFinish: () => {
          UserLoginForm.password = '' //リクエスト終了時にパスワード欄をリセット
        },
        preserveScroll: true, //スクロール位置を維持
      })
    }
  })
}

</script>
<template>
  <Head title="ユーザーログイン" />

  <main class="bg-[url(/images/theater.jpg)] bg-cover bg-center h-screen pt-24 bg-blend-lighten  bg-white/20">
    <div class="max-w-4xl bg-white m-auto rounded-md text-center">

      <h1 class="text-5xl font-bold text-red-600 font-['Oswald',serif] pt-6 pb-3">
        <Link :href="route('home')">
          CINEMA-HOUSE
        </Link>
      </h1>

      <p class="text-xl">CINEMA-HOUSEのアカウントにログイン</p>

      <div class="px-12 mt-3" v-if="Object.keys(UserLoginForm.errors).length" >
        <el-alert title="入力に不備があります。下記をご確認ください。" type="error" show-icon
            :closable="false" >
          <ul class="text-sm text-red-700 list-disc list-inside">
            <li class="text-left" v-for="(message, field) in UserLoginForm.errors" :key="field">
              {{ message }}
            </li>
          </ul>
        </el-alert>
      </div>

      <div class="px-12 py-8">
        <ElForm
          ref="userLoginFormRef"
          :model="UserLoginForm"
          :rules="userLoginRules"
          label-position="top"
          size="large"
          @submit.prevent="submitUserLogin(userLoginFormRef)"
        >
          <ElFormItem label="メールアドレス" prop="email">
            <ElInput v-model="UserLoginForm.email" placeholder="メールアドレスを入力" />
          </ElFormItem>

          <ElFormItem label="パスワード" prop="password">
            <ElInput v-model="UserLoginForm.password" type="password" placeholder="パスワードを入力" show-password />
          </ElFormItem>

          <ElFormItem>
            <ElButton 
              type="primary" 
              @click="submitUserLogin(userLoginFormRef)"
              native-type="submit" 
              class="w-full mt-3"
              :loading="UserLoginForm.processing"
            >
              ログイン
            </ElButton>
          </ElFormItem>
        </ElForm>
      </div>

    </div>
  </main>
</template>
