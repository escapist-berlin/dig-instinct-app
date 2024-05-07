<script setup>
import { ref, reactive } from 'vue';
import { Head, Link, router } from '@inertiajs/vue3';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';

const { lists } = defineProps({
  lists: {
    type: Object,
    required: true
  }
});

const headers = reactive([
{ title: 'Actions', key: 'actions', sortable: false },
  { title: 'Name', key: 'name', sortable: true },
  { title: 'Releases', key: 'releases_count', sortable: true },
  { title: 'Creation Date', key: 'created_at', sortable: true },
  { title: 'Last Updated', key: 'updated_at', sortable: true }
]);

function timeAgo(dateString) { // TODO: Move to globals
  const date = new Date(dateString);
  const now = new Date();
  const seconds = Math.floor((now - date) / 1000);
  const minutes = Math.floor(seconds / 60);
  const hours = Math.floor(minutes / 60);
  const days = Math.floor(hours / 24);
  const weeks = Math.floor(days / 7);
  const months = Math.floor(days / 30);
  const years = Math.floor(days / 365);

  if (years > 0) return `${years} year${years > 1 ? 's' : ''} ago`;
  if (months > 0) return `${months} month${months > 1 ? 's' : ''} ago`;
  if (weeks > 0) return `${weeks} week${weeks > 1 ? 's' : ''} ago`;
  if (days > 0) return `${days} day${days > 1 ? 's' : ''} ago`;
  if (hours > 0) return `${hours} hour${hours > 1 ? 's' : ''} ago`;
  if (minutes > 0) return `${minutes} minute${minutes > 1 ? 's' : ''} ago`;
  if (seconds > 0) return `${seconds} second${seconds > 1 ? 's' : ''} ago`;
  return 'just now';
}
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
          <v-data-table
            :headers="headers"
            :items="lists"
          >
            <template v-slot:item.actions="{ item }">
              <Link :href="route('user-lists.show', item.id)">
                <v-icon
                  class="me-2"
                  size="small"
                  color="primary"
                >
                  mdi-eye
                </v-icon>
              </Link>
            </template>
            <template v-slot:item.created_at="{ item }">
              {{ timeAgo(item.created_at) }}
            </template>
            <template v-slot:item.updated_at="{ item }">
              {{ timeAgo(item.updated_at) }}
            </template>
          </v-data-table>
        </div>
      </div>
    </div>
  </AuthenticatedLayout>
</template>