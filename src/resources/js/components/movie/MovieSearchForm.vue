<script setup lang="ts">
import { router } from '@inertiajs/vue3'

const props = defineProps<{
  genres: { value: string; label: string }[],
  routeName: string // 管理者 or ユーザーのルート名を渡す
}>()

// 親から双方向バインディングされる form
const model = defineModel<{
  title: string
  genre: string | number
  description: string
  search_type: string
}>()

const submitSearch = () => {
  router.get(route(props.routeName), model.value, {
    preserveState: true,
    replace: true,
  })
  
}

const reset = () => {
  model.value = { title: '', genre: '', description: '', search_type: 'partial' }
  router.get(route(props.routeName), {}, { replace: true })
}



</script>
<template>
  <el-card class="mb-6">
    <template #header>
      <span>検索条件</span>
    </template>

    <el-form :inline="true" >
      <el-form-item label="タイトル">
        <el-input v-model="model!.title" placeholder="タイトルを入力" style="width: 600px" clearable />
      </el-form-item>

      <el-form-item label="検索方法">
        <el-radio-group v-model="model!.search_type">
          <el-radio label="partial">あいまい</el-radio>
          <el-radio label="exact">完全一致</el-radio>
        </el-radio-group>
      </el-form-item>

      <el-form-item label="ジャンル">
        <el-select v-model="model!.genre" placeholder="すべて" clearable style="width: 180px">
          <el-option label="すべて" value="" />
          <el-option
            v-for="genre in genres"
            :key="genre.value"
            :label="genre.label"
            :value="genre.value"
          />
        </el-select>
      </el-form-item>

      <el-form-item label="説明文">
        <el-input v-model="model!.description" placeholder="説明文を入力" style="width: 800px" clearable />
      </el-form-item>

      <el-form-item>
        <el-button type="primary" @click="submitSearch">検索</el-button>
        <el-button @click="reset">リセット</el-button>
      </el-form-item>
    </el-form>
  </el-card>
</template>