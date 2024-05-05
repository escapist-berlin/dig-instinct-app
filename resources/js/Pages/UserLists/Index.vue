<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head } from '@inertiajs/vue3';

const { lists } = defineProps({
  lists: {
    type: Object,
    required: true
  }
});
</script>

<template>
  <Head title="Lists" />

  <AuthenticatedLayout>
    <template #header>
      <h2 class="font-semibold text-xl text-gray-800 leading-tight">Lists</h2>
    </template>

    <div class="py-12">
      <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
          // TODO: user list index to create / update / destroy
        </div>
      </div>
    </div>
  </AuthenticatedLayout>
</template>