<script setup lang="ts">
import { ElButton } from 'element-plus'
import { computed } from 'vue'
import { Link, router, usePage } from '@inertiajs/vue3'

// 管理者の名前を取得
const page = usePage()
const admin = computed(() => page.props.auth.admin)

const adminLogout = (): void => {
  router.visit(route('admin.logout'), {
    method: 'post', 
    replace: true // 再読み込みをせずに即座にログイン画面へ遷移 戻るボタンで戻れなくなり、UXが向上
  })
}
</script>
<template>
  <header class="w-full bg-gray-800 text-white px-6 py-4 flex items-center justify-between shadow">

    <div class="flex items-center">
      <Link :href="route('admin.dashboard')" class="text-lg font-bold tracking-wide mr-8">
        CINEMA-HOUSE 管理者TOP
      </Link>
  
      <nav class="space-x-6 text-sm font-medium hidden md:flex">
        <Link :href="route('admin.movies.index')" class="hover:underline">映画一覧</Link >
        <Link href="#" class="hover:underline">上映カレンダー</Link>
        <Link href="#" class="hover:underline">ユーザー管理</Link>
      </nav>
    </div>

    <div class="flex items-center space-x-4 text-sm">
      <span>管理者: {{ admin.name }}</span>
      <el-button size="small" type="info" plain v-on:click="adminLogout">ログアウト</el-button>
    </div>
  </header>
</template>