<script setup lang="ts">
import { Head, useForm } from '@inertiajs/vue3'
import AdminLayout from '@/layouts/AdminLayout.vue'
import type { FormInstance, FormRules } from 'element-plus'
import { ref } from 'vue'
import { onBeforeUnmount } from 'vue'

defineOptions({
  layout: AdminLayout
})

const movieFormRef = ref<FormInstance>()

// ポスター画像プレビュー用
const posterPreviewUrl = ref<string | null>(null)

interface MovieForm {
  title: string
  genre: string
  description: string
  poster: File | null
}

const movieForm = useForm<MovieForm>({
  title: '',
  genre: '',
  description: '',
  poster: null,
})

const props = defineProps<{
  genres: { value: number; label: string }[]
}>()

// 画像プレビュー機能(メモリリーク対策済み)
const handlePosterChange = (e: Event) => {
  const file = (e.target as HTMLInputElement).files?.[0] ?? null

  movieForm.poster = file

  // 画像を選び直した際、古い blob URL を破棄
  if (posterPreviewUrl.value) {
    URL.revokeObjectURL(posterPreviewUrl.value)
  }

  // プレビューURL生成
  posterPreviewUrl.value = file
    ? URL.createObjectURL(file)
    : null
}

// 画面離脱時もblob URL を破棄
onBeforeUnmount(() => {
  if (posterPreviewUrl.value) {
    URL.revokeObjectURL(posterPreviewUrl.value)
  }
})

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
  movieForm.post(route('admin.movies.store'), {
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
  <Head title="映画新規登録" />
  
  <div class="p-6 max-w-4xl mx-auto">
    <h1 class="text-2xl font-bold mb-6">映画新規登録</h1>

    <!-- バックエンド側のバリデーションエラーメッセージ -->
    <el-alert v-if="Object.keys(movieForm.errors).length" title="入力に不備があります。下記をご確認ください。" type="error" show-icon :closable="false" >
      <ul class="text-sm text-red-700 list-disc list-inside">
        <li v-for="(message, field) in movieForm.errors" :key="field">
          {{ message }}
        </li>
      </ul>
    </el-alert>

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
            :value="String(genre.value)"
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

      <!-- ポスター画像 -->
      <el-form-item label="ポスター画像">
        <input
          type="file"
          accept="image/*"
          @change="handlePosterChange"
          class="block w-full text-sm text-gray-700
                file:mr-4 file:py-2 file:px-4
                file:rounded file:border-0
                file:text-sm file:font-semibold
                file:bg-blue-50 file:text-blue-700
                hover:file:bg-blue-100"
        />

        <!-- プレビュー -->
        <div v-if="posterPreviewUrl" class="mt-4">
          <img
            :src="posterPreviewUrl"
            alt="ポスタープレビュー"
            class="w-48 rounded shadow"
          />
        </div>
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
<style scoped>
.el-alert {
  margin-bottom: 8px;
}
</style>
