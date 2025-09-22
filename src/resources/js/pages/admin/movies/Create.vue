<script setup lang="ts">
import { Head, useForm } from '@inertiajs/vue3'
import AdminLayout from '@/layouts/AdminLayout.vue'
import type { FormInstance, FormRules } from 'element-plus'
import { ref } from 'vue'

defineOptions({
  layout: AdminLayout
})

const movieFormRef = ref<FormInstance>()

interface MovieForm {
  title: string
  genre: string
  description: string
}

const movieForm = useForm<MovieForm>({
  title: '',
  genre: '',
  description: '',
})

const props = defineProps<{
  genres: { value: number; label: string }[]
}>()

const confirmDialogVisible = ref(false)

// バリデーション
const storeMovieRules: FormRules<MovieForm> = {
  title: [
    { required: true, message: 'タイトルは必須項目です', trigger: 'blur' },
  ],
  genre: [
    { required: true, message: 'ジャンルは必須項目です', trigger: 'change' },
  ],
  description: [
    { required: true, message: '説明文は必須項目です', trigger: 'blur' },
  ],
}

// 登録ボタンクリック時 → バリデーション → OKならモーダル表示
const handleOpenConfirm = async(formEl: FormInstance | undefined) => {
  if (!formEl) return
  await formEl.validate((valid, fields) => {
    if (valid) {
      confirmDialogVisible.value = true
    }
  })
}

// モーダルの送信処理
const submitMovieStore = () => {
  console.log('登録POST処理')
}

</script>

<template>
  <Head title="映画新規登録" />
  
  <div class="p-6 max-w-4xl mx-auto">
    <h1 class="text-2xl font-bold mb-6">映画新規登録</h1>

    <el-form 
      label-position="top" 
      ref="movieFormRef"
      :model="movieForm" 
      :rules="storeMovieRules"
      class="space-y-6"
    >
      <!-- タイトル -->
      <el-form-item label="映画タイトル" prop="title" >
        <el-input v-model="movieForm.title" placeholder="タイトルを入力" clearable />
      </el-form-item>

      <!-- ジャンル -->
      <el-form-item label="ジャンル" prop="genre" >
        <el-select v-model="movieForm.genre" placeholder="ジャンルを選択" clearable class="w-full">
          <el-option
            v-for="genre in props.genres"
            :key="genre.value"
            :label="genre.label"
            :value="genre.value"
          />
        </el-select>
      </el-form-item>

      <!-- 説明文 -->
      <el-form-item label="説明文" prop="description" >
        <el-input
          type="textarea"
          v-model="movieForm.description"
          :rows="6"
          placeholder="説明文を入力"
          clearable
        />
      </el-form-item>

      <!-- ボタン -->
      <div class="flex justify-end">
        <el-button 
          type="primary" 
          :loading="movieForm.processing"
          @click="handleOpenConfirm(movieFormRef)"
        >
          登録
        </el-button>
      </div>

      <!-- モーダル -->
      <el-dialog
        v-model="confirmDialogVisible"
        title="映画新規登録"
        width="400px"
        :show-close="false"
      >
        <p>入力内容を送信してよろしいですか？</p>

        <template #footer>
          <el-button @click="confirmDialogVisible = false">キャンセル</el-button>
          <el-button type="primary" @click="submitMovieStore" >送信</el-button>
        </template>
      </el-dialog>

    </el-form>
  </div>
</template>
