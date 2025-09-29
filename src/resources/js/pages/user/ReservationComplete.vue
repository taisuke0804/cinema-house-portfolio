<script setup lang="ts">
import { Head, Link } from '@inertiajs/vue3'
import UserLayout from '@/layouts/UserLayout.vue'

defineOptions({
  layout: UserLayout
})
</script>

<template>
  <Head title="予約完了" />

  <div class="max-w-2xl mx-auto py-20 text-center">
    <h1 class="text-2xl font-bold text-green-600 mb-6">予約が完了しました</h1>

    <p class="mb-8 text-gray-700">
      ご予約ありがとうございます。<br />
      ご来場を心よりお待ちしております。
    </p>

    <Link
      :href="route('user.screenings')"
      class="inline-block bg-blue-600 text-white px-6 py-2 rounded hover:opacity-80 transition"
    >
      上映スケジュールへ戻る
    </Link>
  </div>
</template>
