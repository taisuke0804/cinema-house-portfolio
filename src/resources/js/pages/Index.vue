<script setup lang="ts">
import { Head, Link } from '@inertiajs/vue3'
</script>

<template>
  <Head title="Home" />

  <main class="bg-[url(/images/theater.jpg)] bg-cover bg-center h-screen pt-24 bg-blend-lighten  bg-white/20">
    <div class="max-w-4xl bg-white m-auto rounded-md text-center">
      <h1 class="text-5xl font-bold text-red-600 font-['Oswald',serif] pt-6 pb-3">
        CINEMA-HOUSE
      </h1>
      <p class="text-xl">ようこそ、映画の世界へ</p>

      <Link :href="route('login')" class="inline-block pt-8 pb-20">
        <el-button type="primary" size="large">
          ログイン
        </el-button>
      </Link>
      
    </div>
  </main>
</template>
