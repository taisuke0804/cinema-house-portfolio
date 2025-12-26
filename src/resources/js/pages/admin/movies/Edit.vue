<script setup lang="ts">
import { Head, useForm } from '@inertiajs/vue3'
import AdminLayout from '@/layouts/AdminLayout.vue'
import type { FormInstance, FormRules } from 'element-plus'
import { ref } from 'vue'

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
  }
  genres: { value: number; label: string }[]
}>()

const movieForm = useForm({
  title: props.movie.title,
  genre: String(props.movie.genre), // セレクトボックスv-modelに渡す値の型を合致させるためキャスト
  description: props.movie.description,
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