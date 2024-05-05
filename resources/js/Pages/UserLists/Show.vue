<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import ReleasesTable from '@/Components/ReleasesTable.vue'
import { Head } from '@inertiajs/vue3';

const { list } = defineProps({
  list: {
    type: Object,
    required: true
  },
  releases: {
    type: Object,
    required: true
  },
});
</script>

<template>
  <Head :title=list.name />

  <AuthenticatedLayout>
    <template #header>
      <h2 class="font-semibold text-xl text-gray-800 leading-tight">{{ list.name }}</h2>
    </template>

    <div class="py-12">
      <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
          <ReleasesTable
            :releases="releases"
          />
        </div>
      </div>
    </div>
  </AuthenticatedLayout>
</template>