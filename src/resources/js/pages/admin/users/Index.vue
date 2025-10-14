<script setup lang="ts">
import { Head, Link, router } from '@inertiajs/vue3'
import AdminLayout from '@/layouts/AdminLayout.vue'
import { ref, watch } from 'vue'
import dayjs from 'dayjs'
import 'dayjs/locale/ja'
dayjs.locale('ja')

defineOptions({
  layout: AdminLayout
})

const props = defineProps<{
  users: {
    data: { id: number; name: string; email: string; created_at: string }[],
    current_page: number,
    last_page: number,
    total: number,
    links: { url: string | null; label: string; active: boolean }[],
    per_page: number,
  }
}>()

const currentPage = ref(props.users.current_page)
// props が更新されたら同期
watch(() => props.users.current_page, (val) => {
  currentPage.value = val
})

// ページ切り替え
const handlePageChange = (page: number) => {
  router.get(route('admin.users.index'), { page }, { preserveState: true })
}

</script>
<template>
  <Head title="ユーザー管理" />

  <div class="p-6">
    <div class="flex justify-between">
      <h1 class="text-2xl font-bold mb-6">ユーザー  一覧</h1>
      <Link :href="route('admin.users.create')">
        <el-button type="success">ユーザー新規登録</el-button>
      </Link>
    </div>

    <el-alert
      v-if="$page.props.flash.success"
      :title="$page.props.flash.success"
      type="success"
      show-icon
      closable
    />

    <el-table :data="props.users.data" stripe border style="width: 100%">
      <el-table-column prop="id" label="ID" width="80" align="center" />
      <el-table-column prop="name" label="名前" />
      <el-table-column prop="email" label="メールアドレス" />
      <el-table-column prop="created_at" label="登録日" width="160" align="center" >
        <template #default="{ row }">
          {{ dayjs(row.created_at).format('YYYY-MM-DD') }}
        </template>
      </el-table-column>
      <el-table-column label="操作" width="120" align="center">
        <template #default="{ row }">
          <Link
            href="#"
            class="el-button el-button--primary el-button--small"
          >
            詳細
          </Link>
        </template>
      </el-table-column>
    </el-table>

    <!-- ページネーション -->
    <div class="flex justify-center mt-6">
      <el-pagination
        background
        layout="prev, pager, next"
        :total="users.total"
        :page-size="props.users.per_page"
        v-model:current-page="currentPage"
        @current-change="handlePageChange"
      />
    </div>

  </div>

</template>
<style scoped>
:deep(.el-alert) {
  margin-bottom: 8px;
}
</style>