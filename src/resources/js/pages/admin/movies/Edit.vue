<script setup lang="ts">
import { Head, useForm } from '@inertiajs/vue3'
import AdminLayout from '@/layouts/AdminLayout.vue'
import type { FormInstance, FormRules } from 'element-plus'
import { ref, onBeforeUnmount } from 'vue'

defineOptions({
  layout: AdminLayout
})

const movieFormRef = ref<FormInstance>()

const props = defineProps<{
  movie: {
    id: number
    title: string
    genre: number
    description: string
    poster_url: string
  }
  genres: { value: number; label: string }[]
}>()

const movieForm = useForm({
  title: props.movie.title,
  genre: String(props.movie.genre), // セレクトボックスv-modelに渡す値の型を合致させるためキャスト
  description: props.movie.description,
  poster: null as File | null,
})

// 新規選択画像のプレビュー用
const posterPreviewUrl = ref<string | null>(null)

const handlePosterChange = (e: Event) => {
  const file = (e.target as HTMLInputElement).files?.[0] ?? null

  movieForm.poster = file

  // 既存の blob URL を破棄
  if (posterPreviewUrl.value) {
    URL.revokeObjectURL(posterPreviewUrl.value)
  }

  posterPreviewUrl.value = file
    ? URL.createObjectURL(file)
    : null
}

onBeforeUnmount(() => {
  if (posterPreviewUrl.value) {
    URL.revokeObjectURL(posterPreviewUrl.value)
  }
})

// バリデーション
const updateMovieRules: FormRules = {
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
const submitMovieUpdate = () => {
  movieForm.put(route('admin.movies.update', props.movie.id), {
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
  <Head title="映画情報編集" />

  <div class="p-6 max-w-4xl mx-auto">
    <h1 class="text-2xl font-bold mb-6">映画情報編集</h1>

    <el-form
      label-position="top"
      ref="movieFormRef"
      :model="movieForm"
      :rules="updateMovieRules"
      class="space-y-6"
    >
  
      <!-- タイトル -->
      <el-form-item label="映画タイトル" prop="title">
        <el-input v-model="movieForm.title" clearable />
      </el-form-item>

      <!-- ジャンル -->
      <el-form-item label="ジャンル" prop="genre">
        <el-select v-model="movieForm.genre" class="w-full">
          <el-option
            v-for="genre in props.genres"
            :key="genre.value"
            :label="genre.label"
            :value="genre.value"
          />
        </el-select>
      </el-form-item>

      <!-- 説明文 -->
      <el-form-item label="説明文" prop="description">
        <el-input
          type="textarea"
          v-model="movieForm.description"
          :rows="6"
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
        <div class="mt-4">
          <!-- 新規選択画像があれば優先表示 -->
          <img
            v-if="posterPreviewUrl"
            :src="posterPreviewUrl"
            alt="新しいポスター画像"
            class="w-48 rounded shadow"
          />

          <!-- 未選択時は既存ポスター -->
          <img
            v-else
            :src="props.movie.poster_url"
            alt="現在のポスター画像"
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
          更新
        </el-button>
      </div>

      <!-- モーダル -->
      <el-dialog
        v-model="confirmDialogVisible"
        title="映画情報編集"
        width="400px"
        :show-close="false"
      >
        <p>入力内容を送信してよろしいですか？</p>

        <template #footer>
          <el-button @click="confirmDialogVisible = false">キャンセル</el-button>
          <el-button type="primary" @click="submitMovieUpdate" >送信</el-button>
        </template>
      </el-dialog>
  
    </el-form>

  </div>

</template>