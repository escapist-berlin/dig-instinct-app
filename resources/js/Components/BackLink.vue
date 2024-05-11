<script setup>
import { router, usePage } from '@inertiajs/vue3';

let urlPrev = usePage().props.urlPrev

const back = () => {
  if (urlPrev) {
    router.visit(urlPrev)
  }
}
</script>

<template>
  <button
    @click="back"
    class="bg-blue-500 hover:bg-blue-700 text-white font-semibold py-1 px-2 text-sm rounded focus:outline-none focus:shadow-outline"
  >
    <slot />
  </button>
</template>