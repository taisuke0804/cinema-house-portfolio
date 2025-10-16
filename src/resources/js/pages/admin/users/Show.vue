<script setup lang="ts">
import AdminLayout from '@/layouts/AdminLayout.vue'
import { Head, Link } from '@inertiajs/vue3'
import dayjs from 'dayjs'
import 'dayjs/locale/ja'
dayjs.locale('ja')

defineOptions({
  layout: AdminLayout
})

defineProps<{
  user: {
    id: number
    name: string
    email: string
    created_at: string
  }
}>()

</script>
<template>
  <Head title="ユーザー詳細" />

  <div class="p-6">
    <h1 class="text-2xl font-bold mb-6">ユーザー詳細</h1>
    <el-card class="max-w-3xl bg-gray-50">
      <div class="flex justify-between items-start">
        <div>
          <h2 class="text-2xl font-bold text-blue-600 mb-2">
            {{ user.name }}
          </h2>
          <p class="font-semibold">メールアドレス: 
            <span class="font-normal text-gray-700">{{ user.email }}</span>
          </p>
          <p class="font-semibold mt-1">
            登録日:
            <span class="font-normal text-gray-700">{{ dayjs(user.created_at).format('YYYY-MM-DD') }}</span>
          </p>
        </div>

        <el-button type="danger" plain>
          削除
        </el-button>

      </div>
    </el-card>

    <div class="mt-6">
      <Link
        :href="route('admin.users.index')"
        class="el-button el-button--default"
      >
        戻る
      </Link>
    </div>

  </div>
</template>