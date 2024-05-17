<script setup>
import { useData } from 'vitepress'
const { page } = useData()
</script>

<template>

  <div v-if="page.isNotFound">
    Custom 404 page!
  </div>

  <Content v-else />

</template>

