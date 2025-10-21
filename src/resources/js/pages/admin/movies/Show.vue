<script setup lang="ts">
import { Head, Link } from '@inertiajs/vue3'
import AdminLayout from '@/layouts/AdminLayout.vue'
import { StarFilled } from '@element-plus/icons-vue'

defineOptions({
  layout: AdminLayout
})

const props = defineProps<{
  movie: {
    id: number
    title: string
    genre_label: string
    description: string
    like_count: number
  }
}>()

</script>
<template>
  <Head title="映画詳細情報" />

  <div class="max-w-5xl mx-auto py-10">
    <h1 class="text-3xl font-bold mb-6">映画詳細</h1>

    <el-card shadow="never" class="mb-6">
      <template #header>
        <span class="font-semibold">映画情報</span>
      </template>

      <div class="space-y-3">
        <p class="text-xl">
          <span class="font-bold mr-2">タイトル: </span>
          『{{ props.movie.title }}』
        </p>

        <p class="text-xl">
          <span class="font-bold mr-2">ジャンル: </span>
          {{ props.movie.genre_label }}
        </p>

        <p class="text-xl">
          <span class="font-bold">説明文: </span>
        </p>
        <p class="whitespace-pre-line text-lg">
          {{ props.movie.description }}
        </p>

        <!-- いいね！ボタン -->
        <div class="mt-8 flex items-center space-x-3">
          <el-icon
            size="28"
            class=" transition-colors duration-300"
          >
            <StarFilled />
          </el-icon>
          <span class="text-gray-600 text-lg">
            {{ props.movie.like_count }} 件のいいね
          </span>
        </div>

      </div>
    </el-card>

    <div class="flex justify-between items-center">
      <Link :href="route('admin.movies.index')">
        <el-button type="info">映画一覧に戻る</el-button>
      </Link>

      <div class="flex gap-4">
        <!-- 削除ボタン 後ほど実装 -->
        <!-- <el-button type="danger">削除</el-button> -->

        <!-- 上映スケジュール登録ボタン -->
        <Link :href="route('admin.screenings.create', props.movie.id)">
          <el-button type="primary">上映スケジュールを登録</el-button>
        </Link>
      </div>
    </div>
  </div>
</template>