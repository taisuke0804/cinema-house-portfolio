<script setup lang="ts">
import UserLayout from '@/layouts/UserLayout.vue'
import { Head, router  } from '@inertiajs/vue3'
import { ref } from 'vue'
import { Star, StarFilled } from '@element-plus/icons-vue'

defineOptions({
  layout: UserLayout
})

const props = defineProps<{
  movie: {
    id: number
    title: string
    genre_label: string
    description: string
    liked: boolean
    like_count: number
    poster_url: string
  }
}>()

// 初期値を Laravel側のpropsから反映
const liked = ref(props.movie.liked)
const likeCount = ref(props.movie.like_count)

// いいねトグル処理（バックエンド通信）
const toggleLike = () => {
  router.post(
    route('user.movies.like', props.movie.id),
    {},
    {
      preserveScroll: true,
      onSuccess: () => {
        liked.value = !liked.value
        likeCount.value += liked.value ? 1 : -1
      },
    }
  )
}

</script>
<template>
  <Head title="映画詳細情報" />

  <div class="max-w-5xl mx-auto py-10">
    <h1 class="text-3xl font-bold mb-6">映画詳細</h1>

    <el-card shadow="never" class="mb-6">
      <template #header>
        <span class="font-semibold">映画情報</span>
      </template>

      <!-- ポスター画像 -->
      <img
        :src="props.movie.poster_url"
        alt="映画ポスター"
        class="w-64 rounded shadow mb-6"
      />

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
            class="cursor-pointer transition-colors duration-300"
            @click="toggleLike"
          >
            <component :is="liked ? StarFilled : Star" />
          </el-icon>

          <span class="text-gray-600 text-lg">
            {{ likeCount }} 件のいいね
          </span>
        </div>



      </div>
    </el-card>

  </div>
</template>