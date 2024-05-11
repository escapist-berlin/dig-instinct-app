<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import BackLink from '@/Components/BackLink.vue';
import { Head, router } from '@inertiajs/vue3';
import { computed, ref, reactive, toRefs } from 'vue';

const { release, favoriteTrackIds, auth } = defineProps({
  release: {
    type: Object,
    required: true
  },
  favoriteTrackIds: {
    type: Array,
    required: true
  },
  auth: {
    type: Object,
    required: true
  },
});

const formatNames = (items) => items.map(item => item.name).join(', ');

const formattedGenres = computed(() => formatNames(release.genres));
const formattedStyles = computed(() => formatNames(release.styles));

const formattedReleaseDate = computed(() => {
  return new Date(release.released).toLocaleDateString('en-US', {
    year: 'numeric',
    month: 'short',
    day: 'numeric'
  });
});

const currentListId = ref(release.user_lists[0]?.id || null);
const userLists = computed(() => auth.lists);

function updateList() {
    router.post(route('releases.update-list', { release: release.id }), {
        list_id: currentListId.value
    }, {
        preserveState: true,
        preserveScroll: true,
        onSuccess: () => {
            console.log('List updated successfully.'); // TODO: Toast Message
        },
        onError: error => {
            console.error('Error updating list:', error); // TODO: Toast Message
        }
    });
}

const tracksState = reactive({
  tracks: release.tracks.map(track => ({
    ...track,
    is_favorite: favoriteTrackIds.includes(track.id)
  }))
});

// Export `tracks` for use in the template - CHECK: Needed?
const { tracks } = toRefs(tracksState);

function toggleLike(track) {
  // Determine the action based on the current favorite status
  const action = track.is_favorite ? 'unlike' : 'like';
  const method = track.is_favorite ? 'delete' : 'post';
  const url = `/releases/${track.release_id}/tracks/${track.id}/${action}`;

  // Common options for both delete and post requests
  const options = {
    preserveScroll: true,
    preserveState: true,
    onSuccess: (page) => {
      track.is_favorite = !track.is_favorite;
    }
  };

  // Call the Inertia delete or post request dynamically
  router[method](url, method === 'post' ? {} : options, method === 'post' ? options : undefined);
}
</script>

<template>
  <Head title="Release" />

  <AuthenticatedLayout>
    <template #header>
      <div class="flex items-center justify-between">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">Release</h2>
        <BackLink>Go Back</BackLink>
      </div>
    </template>

    <div class="bg-gray-100 p-4">
      <div class="max-w-2xl mx-auto bg-white shadow-md rounded p-6">
        <div class="flex flex-col md:flex-row">
          <div class="md:w-1/3">
            <img :src="release.image_full_uri" alt="Cover Art" class="rounded">
          </div>
          <div class="md:w-2/3 md:pl-4">
            <h2 class="text-xl font-bold">
              <span v-for="(artist, index) in release.artists" :key="artist.id">
                <a :href="`https://www.discogs.com/artist/${artist.discogs_id}`" class="hover:underline" target="_blank">
                  {{ artist.name }}
                </a>
                <template v-if="index !== release.artists.length - 1">, </template>
              </span>
              - <a :href="release.uri" class="hover:underline" target="_blank">{{ release.title }}</a>
            </h2>
            <p class="text-600">
              Label:
              <span v-for="(label, index) in release.labels" :key="label.id">
                <a :href="`https://www.discogs.com/label/${label.discogs_id}`" class="hover:underline" target="_blank">
                  {{ label.name }}
                </a>
                - {{ label.pivot.catno }}
                <template v-if="index !== release.labels.length - 1">, </template>
              </span>
            </p>
            <p class="text-600">Format: {{ release.formats }}</p>
            <p class="text-600">Country: {{ release.country }}</p>
            <p class="text-600">Released: {{ formattedReleaseDate }}</p>
            <p class="text-600">Genre: {{ formattedGenres }}</p>
            <p class="text-600">Style: {{ formattedStyles }}</p>
          </div>
        </div>
        <div class="flex flex-wrap md:flex-nowrap justify-between mt-6">
          <div class="md:w-2/3">
            <h3 class="font-semibold mb-2">Tracklist:</h3>
            <ul>
              <li v-for="track in tracks" :key="track.id">
                <span>{{ track.position }}.
                  <span v-if="track.artists.length">
                    <span v-for="(artist, index) in track.artists" :key="artist.id">
                      <a :href="`https://www.discogs.com/artist/${artist.discogs_id}`" class="hover:underline" target="_blank">
                        {{ artist.name }}
                      </a>
                      <template v-if="index !== track.artists.length - 1">, </template>
                    </span>
                    -
                  </span>
                  {{ track.title }}

                  <button @click="toggleLike(track)">
                    <span v-if="track.is_favorite" class="text-red-500">
                      ♥
                    </span>
                    <span v-else class="text-gray-400">
                      ♡
                    </span>
                  </button>
                </span>
              </li>
            </ul>
          </div>
        </div>
        <div class="p-4 mt-4 flex flex-col sm:flex-row items-center bg-white shadow rounded text-sm">
          <p class="m-0"><strong>Have:</strong> {{ release.have }}</p>
          <span class="hidden sm:inline-block mx-2">|</span>
          <p class="m-0"><strong>Want:</strong> {{ release.want }}</p>
          <span class="hidden sm:inline-block mx-2">|</span>
          <p class="m-0"><strong>Rating:</strong> {{ release.rating_average }} / 5 ({{ release.rating_count }} votes)</p>
          <span v-if="release.num_for_sale && release.lowest_price" class="hidden sm:inline-block mx-2">|</span>
          <p class="m-0" v-if="release.num_for_sale && release.lowest_price">{{ release.num_for_sale }}<strong> copies from</strong> ${{ release.lowest_price }}</p>
          <span v-if="release.kollektivx_uri" class="hidden sm:inline-block mx-2">|</span>
          <a v-if="release.kollektivx_uri" :href="release.kollektivx_uri" target="_blank" class="text-blue-500 hover:text-blue-700">KollektivX</a>
        </div>

        <div class="p-4 mt-4 flex flex-col sm:flex-row items-center bg-white shadow rounded text-sm">
          <label for="userListSelect" class="block mb-2 text-sm font-medium text-gray-900">Current List:</label>
          <select
            id="userListSelect"
            v-model="currentListId"
            @change="updateList"
            class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5">
            <option v-for="list in userLists" :key="list.id" :value="list.id">{{ list.name }}</option>
          </select>
        </div>
      </div>
    </div>
  </AuthenticatedLayout>
</template>