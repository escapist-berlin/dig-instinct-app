<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import ReleasesTable from '@/Components/ReleasesTable.vue'
import { Head, usePage, router } from '@inertiajs/vue3';
import { ref } from 'vue';

const { list, releases } = defineProps({
  list: {
    type: Object,
    required: true
  },
  releases: {
    type: Object,
    required: true
  },
});

const isRefreshing = ref(false);

const refreshDiscogsWantlist = () => {
  isRefreshing.value = true;
    router.post(route('discogs.updateWantlist'), {}, {
      preserveScroll: true,
      onSuccess: () => {
        isRefreshing.value = false;
        console.log('Wantlist updated successfully');
      },
      onError: (error) => {
        isRefreshing.value = false;
        console.error('Failed to refresh wantlist:', error);
      }
    });
};
</script>

<template>
  <Head :title=list.name />

  <AuthenticatedLayout>
    <template #header>
      <div class="flex">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">{{ list.name }}</h2>
        <button
          v-if="list.name === 'Discogs Wantlist' && $page.props.auth.user.discogs_connected"
          @click="refreshDiscogsWantlist"
          class="flex items-center ml-2">
          <img :class="{'animate-spin': isRefreshing}" src="/icons/sync.svg" alt="Sync Icon" class="h-6 w-6" />
        </button>
      </div>
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