<script setup>
import { ref, reactive, nextTick } from 'vue';
import { Head, Link, router } from '@inertiajs/vue3';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import BackLink from '@/Components/BackLink.vue';
import { timeAgo } from '@/utils/timeAgo';

const props = defineProps({
  lists: {
    type: Object,
    required: true
  }
});

const headers = [
  { title: 'Name', key: 'name', sortable: true },
  { title: 'Releases', key: 'releases_count', sortable: true },
  { title: 'Creation Date', key: 'created_at', sortable: true },
  { title: 'Last Updated', key: 'updated_at', sortable: true },
  { key: 'delete_action', sortable: false },
];
const listsWithEditing = reactive(props.lists.map(list => ({
  ...list,
  isEditing: false
})));
const nameInput = ref(null);

function focusInput() {
  nextTick(() => {
    nameInput.value?.focus();
  });
}

function addNewList() {
  listsWithEditing.push({
    id: null, // No ID until it's saved to the database
    name: '',
    isEditing: true
  });
  focusInput();
}

function toggleEditing(list) {
  list.isEditing = !list.isEditing;
  if (list.isEditing) focusInput();
}

function createOrUpdateList(list) {
  let url = '/user-lists';
  let method = 'post';

  // for update request
  if (list.id) {
    url += `/${list.id}`;
    method = 'patch';
  }

  router[method](url, list, {
    preserveState: true,
    onSuccess: (page) => {
      if (!list.id) {
        list.id = page.props.lists[page.props.lists.length - 1].id;
        list.releases_count = page.props.lists[page.props.lists.length - 1].releases_count;
      }
      list.isEditing = false;
    },
    onError: (errors) => {
      console.log(errors); // TODO: Error Handling & Validation
    },
  });
}

function deleteList(item) {
  router.delete(`/user-lists/${item.id}`, {
    onBefore: () => confirm(`Are you sure you want to delete '${item.name}' list?`),
    onSuccess: (page) => {
      // Update local state by filtering out the deleted list
      const deletedListIndex = listsWithEditing.findIndex(list => list.id === item.id);
      if (deletedListIndex > -1) {
        listsWithEditing.splice(deletedListIndex, 1);
      }
    },
    onError: (error) => {
      console.error('Error deleting the list:', error); // TODO: Error Handling
    }
  });
}
</script>

<template>
  <Head title="Lists" />

  <AuthenticatedLayout>
    <template #header>
      <div class="flex items-center justify-between">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">Lists</h2>
        <BackLink>Go Back</BackLink>
      </div>
    </template>

    <div class="py-12">
      <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
          <v-data-table
            :headers="headers"
            :items="listsWithEditing"
          >
            <template v-slot:top>
              <v-toolbar flat>
                <v-toolbar-title>My Lists</v-toolbar-title>
                <v-spacer />
                <v-btn
                  class="mb-2"
                  color="primary"
                  @click="addNewList"
                >
                  New List
              </v-btn>
              </v-toolbar>
            </template>

            <template #item.name="{ item }">
              <div v-if="item.isEditing">
                <v-icon
                  v-if="!item.isEditing"
                  @click="toggleEditing(item)"
                  class="mr-2"
                  color="primary"
                  size="small"
                >
                  mdi-pencil
                </v-icon>

                <v-icon
                  v-if="item.isEditing"
                  @click="createOrUpdateList(item)"
                  class="mr-2"
                  color="primary"
                  size="small"
                  >
                  mdi-content-save
                </v-icon>

                <input
                  v-model="item.name"
                  placeholder="Edit Name"
                  outlined
                  ref="nameInput"
                />
              </div>

              <div v-else>
                <v-icon
                  v-if="!item.isEditing"
                  @click="toggleEditing(item)"
                  class="mr-2"
                  color="primary"
                  size="small"
                >
                  mdi-pencil
                </v-icon>

                <v-icon
                  v-if="item.isEditing"
                  @click="createOrUpdateList(item)"
                  class="mr-2"
                  color="primary"
                  size="small"
                  >
                  mdi-content-save
                </v-icon>

                <Link
                  :href="route('user-lists.show', { user_list: item.id })"
                  class="hover:underline"
                  >
                  {{ item.name }}
                </Link>
              </div>
            </template>

            <template v-slot:item.created_at="{ item }">
              {{ timeAgo(item.created_at) }}
            </template>
            <template v-slot:item.updated_at="{ item }">
              {{ timeAgo(item.updated_at) }}
            </template>

            <template v-slot:item.delete_action="{ item }">
              <v-icon
                @click="deleteList(item)"
                class="mr-2"
                color="primary"
                ssize="small"
                >
                mdi-delete
              </v-icon>
            </template>
          </v-data-table>
        </div>
      </div>
    </div>
  </AuthenticatedLayout>
</template>