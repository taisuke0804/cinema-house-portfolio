<script setup lang="ts">
import { Head, Link, router, usePage  } from '@inertiajs/vue3'
import AdminLayout from '@/layouts/AdminLayout.vue'
import { reactive, ref, watch, computed } from 'vue'

interface SearchForm {
  title: string
  genre: number | string
  description: string
  search_type: string
}

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
  },
  filters: {
    title?: string,
    genre?: number | string,
    description?: string,
    search_type?: string
  },
  genres: { value: number; label: string }[]
}>()

// 検索フォーム
const searchForm = reactive({
  title: props.filters?.title || '',
  genre: props.filters?.genre || '',
  description: props.filters?.description || '',
  search_type: props.filters?.search_type || 'partial',
})

const currentPage = ref(props.movies.current_page)
// props が更新されたら同期
watch(() => props.movies.current_page, (val) => {
  currentPage.value = val
})

// 検索処理
const submitSearch = () => {
  router.get(route('admin.movies.index'), searchForm, {
    preserveState: true,
    replace: true,
  })
}

// リセット処理
const reset = () => {
  searchForm.title = ''
  searchForm.genre = ''
  searchForm.description = ''
  searchForm.search_type = 'partial'
  router.get(route('admin.movies.index'), {}, { replace: true })
}

// ページ切り替え
const handlePageChange = (page: number) => {
  router.get(route('admin.movies.index'), { ...searchForm, page }, { preserveState: true })
}
</script>

<template>
  <Head title="映画一覧" />
  
  <div class="p-6">
    <div class="flex justify-between">
      <h1 class="text-2xl font-bold mb-6">映画一覧</h1>
      <Link :href="route('admin.movies.create')">
        <el-button type="success">新規登録</el-button>
      </Link>
    </div>

    <!-- フラッシュメッセージ -->
    <el-alert
      v-if="$page.props.flash.success"
      :title="$page.props.flash.success"
      type="success"
      show-icon
      closable
    />

    <el-card class="mb-6">
      <template #header>
        <span>検索条件を表示</span>
      </template>

      <el-form :inline="true" @submit.prevent="submitSearch" >
        <el-form-item label="タイトル" >
          <el-input v-model="searchForm.title" style="width: 800px;" placeholder="タイトルを入力" clearable />
        </el-form-item>

        <el-form-item label="タイトル検索方法">
          <el-radio-group v-model="searchForm.search_type" >
            <el-radio label="partial">あいまい</el-radio>
            <el-radio label="exact">完全一致</el-radio>
          </el-radio-group>
        </el-form-item>
  
        <el-form-item label="ジャンル">
          <el-select v-model="searchForm.genre" placeholder="すべて" clearable style="width: 180px">
            <el-option label="すべて" value="" />
            <el-option 
              v-for="genre in props.genres"
              :key="genre.value"
              :label="genre.label"
              :value="genre.value"
            />
          </el-select>
        </el-form-item>
  
        <el-form-item label="説明文">
          <el-input v-model="searchForm.description" style="width: 1000px;" placeholder="説明文を入力" clearable />
        </el-form-item>

        <el-form-item>
          <el-button type="primary" @click="submitSearch" native-type="submit" >検索</el-button>
          <el-button @click="reset" >リセット</el-button>
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
<style scoped>
.el-alert {
  margin-bottom: 4px;
}
</style>