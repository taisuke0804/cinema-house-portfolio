<script setup lang="ts">
import { Head, useForm } from '@inertiajs/vue3'
import AdminLayout from '@/layouts/AdminLayout.vue'

defineOptions({
  layout: AdminLayout
})

interface MovieForm {
  title: string
  genre: string
  description: string
}

// フォーム
const movieForm = useForm<MovieForm>({
  title: '',
  genre: '',
  description: '',
})

const props = defineProps<{
  genres: { value: number; label: string }[]
}>()

// 送信処理
const submitMovieStore = () => {
  console.log('submit')
}
</script>

<template>
  <Head title="映画新規登録" />
  
  <div class="p-6 max-w-4xl mx-auto">
    <h1 class="text-2xl font-bold mb-6">映画新規登録</h1>

    <el-form label-position="top" @submit.prevent="submitMovieStore" class="space-y-6">
      <!-- タイトル -->
      <el-form-item label="映画タイトル">
        <el-input v-model="movieForm.title" placeholder="タイトルを入力" clearable />
      </el-form-item>

      <!-- ジャンル -->
      <el-form-item label="ジャンル">
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
      <el-form-item label="説明文">
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
          native-type="submit" 
          :loading="movieForm.processing"
        >
          登録
        </el-button>
      </div>
    </el-form>
  </div>
</template>
