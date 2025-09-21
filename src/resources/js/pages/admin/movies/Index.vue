<script setup lang="ts">
import { Head, Link, router } from '@inertiajs/vue3'
import AdminLayout from '@/layouts/AdminLayout.vue'
import { ref, watch } from 'vue'

defineOptions({
  layout: AdminLayout
})

const props = defineProps<{
  movies: {
    data: { id: number; title: string; genre: number; genre_label: string; description: string }[],
    current_page: number,
    last_page: number,
    total: number,
    links: { url: string | null; label: string; active: boolean }[],
    per_page: number,
  }
}>()

const currentPage = ref(props.movies.current_page)

// props が更新されたら同期
watch(() => props.movies.current_page, (val) => {
  currentPage.value = val
})

const handlePageChange = (page: number) => {
  router.get(route('admin.movies.index'), { page }, { preserveState: true }) // preserveState: true ページ遷移しても現在の状態を維持
}

</script>
<template>
  <Head title="映画一覧" />
  
  <div class="p-6">
    <h1 class="text-2xl font-bold mb-6">映画一覧</h1>

    <el-card class="mb-6">
      <template #header>
        <span>検索条件を表示</span>
      </template>

      <el-form :inline="true" >
        <el-form-item label="タイトル" >
          <el-input style="width: 800px;" placeholder="タイトルを入力" clearable />
        </el-form-item>

        <el-form-item label="タイトル検索方法">
          <el-radio-group >
            <el-radio label="partial">あいまい</el-radio>
            <el-radio label="exact">完全一致</el-radio>
          </el-radio-group>
        </el-form-item>
  
        <el-form-item label="ジャンル">
          <el-select placeholder="すべて" clearable style="width: 180px">
            <el-option label="すべて" value="" />
            <el-option label="アクション" :value="1" />
            <el-option label="コメディ" :value="2" />
            <el-option label="ドラマ" :value="3" />
          </el-select>
        </el-form-item>
  
        <el-form-item label="説明文">
          <el-input style="width: 1000px;" placeholder="説明文を入力" clearable />
        </el-form-item>

        <el-form-item>
          <el-button type="primary" >検索</el-button>
          <el-button >リセット</el-button>
        </el-form-item>

      </el-form>

    </el-card>

    <el-table :data="props.movies.data" border stripe class="w-full">
      <el-table-column prop="title" label="タイトル" min-width="200" />
      <el-table-column prop="genre_label" label="ジャンル" min-width="120" />
      <el-table-column prop="description" label="説明文" min-width="300" show-overflow-tooltip />
      <el-table-column label="操作" width="150">
        <template #default="scope">
          <Link href="#">
            <el-button type="primary" size="small">詳細</el-button>
          </Link>
        </template>
      </el-table-column>
    </el-table>

    <!-- ページネーション -->
    <div class="flex justify-center mt-6">
      <el-pagination background layout="prev, pager, next" 
        :total="props.movies.total" 
        v-model:current-page="currentPage"
        :page-size="props.movies.per_page"
        @current-change="handlePageChange"
      />
    </div>
  
  </div>
</template>