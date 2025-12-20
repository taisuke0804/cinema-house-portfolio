<script setup lang="ts">
import { Head } from '@inertiajs/vue3'
import UserLayout from '@/layouts/UserLayout.vue'

defineOptions({
  layout: UserLayout
})
</script>
<template>
  <Head title="ユーザートップページ" />
  <div>
    <h1 class="text-2xl font-bold mb-6 text-center">ユーザートップページ</h1>

    <div class="max-w-2xl mx-auto bg-white shadow rounded-md">
      <div class="bg-blue-600 text-white font-semibold px-4 py-2 rounded-t-md">
        アプリの概要
      </div>
      <ul class="divide-y divide-gray-200 px-6 py-4 text-left text-gray-700">
        <li class="py-2">プライベートシネマの座席予約アプリ</li>
        <li class="py-2">上映スケジュールページから日付・作品を選び予約する</li>
        <li class="py-2">予約した内容は座席予約一覧ページから確認</li>
      </ul>
    </div>
  </div>
</template>