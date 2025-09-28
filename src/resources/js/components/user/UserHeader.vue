<script setup lang="ts">
import { ElButton } from 'element-plus'
import { computed } from 'vue'
import { Link, router, usePage } from '@inertiajs/vue3'

// ユーザー情報を取得
const page = usePage()
const user = computed(() => page.props.auth.user)

const userLogout = (): void => {
  router.visit(route('logout'), {
    method: 'post',
    replace: true, // 戻るボタンでログイン画面に戻らないようにする
  })
}
</script>
<template>
  <header class="w-full bg-gray-600 text-white px-6 py-4 flex items-center justify-between shadow">
    <div class="flex items-center">
      <Link :href="route('dashboard')" class="text-xl font-bold tracking-wide mr-8">
        CINEMA-HOUSE
      </Link>

      <nav class="space-x-6 text-sm font-medium hidden md:flex">
        <Link :href="route('user.screenings')" class="hover:underline">上映スケジュール</Link>
        <Link href="#" class="hover:underline">座席予約一覧</Link>
      </nav>

    </div>

    <div class="flex items-center space-x-4 text-sm">
      <span>ようこそ、<span class="font-semibold">{{ user?.name }}</span> さん</span>
      <ElButton size="small" type="info" plain @click="userLogout">
        ログアウト
      </ElButton>
    </div>
  </header>
</template>