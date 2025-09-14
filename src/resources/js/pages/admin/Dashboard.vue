<script setup lang="ts">
import { Head, router  } from '@inertiajs/vue3'
import AdminLayout from '@/layouts/AdminLayout.vue'

// レイアウトを適用
defineOptions({
  layout: AdminLayout
})

const adminLogout = (): void => {
  router.visit(route('admin.logout'), {
    method: 'post', 
    replace: true // 再読み込みをせずに即座にログイン画面へ遷移 戻るボタンで戻れなくなり、UXが向上
  })
}
</script>

<template>
  <Head title="管理者ダッシュボード" />

  <main class="max-w-4xl mx-auto py-12 px-4">
    <h1 class="text-3xl font-bold mb-4">管理者ダッシュボード</h1>

    <p class="text-lg mb-6 text-gray-700">ようこそ、管理者ページへログインしました。</p>

    <el-button type="primary" v-on:click="adminLogout">
      ログアウト
    </el-button>
  </main>
</template>
