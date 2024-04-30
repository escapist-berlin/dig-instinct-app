<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head } from '@inertiajs/vue3';
import { computed } from 'vue';

const { release } = defineProps({
  release: {
    type: Object,
    required: true
  }
});

const formatNames = (items) => items.map(item => item.name).join(', ');

const formatLabels = (labels) => {
  return labels.map(label => `${label.name} - ${label.pivot.catno}`).join(', ');
};

const formattedArtists = computed(() => formatNames(release.artists));
const formattedLabels = computed(() => formatLabels(release.labels));
const formattedGenres = computed(() => formatNames(release.genres));
const formattedStyles = computed(() => formatNames(release.styles));

const formattedReleaseDate = computed(() => {
  return new Date(release.released).toLocaleDateString('en-US', {
    year: 'numeric',
    month: 'short',
    day: 'numeric'
  });
});
</script>

<template>
  <Head title="Release" />

  <AuthenticatedLayout>
    <template #header>
      <h2 class="font-semibold text-xl text-gray-800 leading-tight">Releases</h2>
    </template>
    <!-- <div>{{ release }}</div> -->

    <div class="bg-gray-100 p-4">
      <div class="max-w-2xl mx-auto bg-white shadow-md rounded p-6">
        <div class="flex flex-col md:flex-row">
          <div class="md:w-1/3">
            <img :src="release.image_full_uri" alt="Cover Art" class="rounded">
          </div>
          <div class="md:w-2/3 md:pl-4">
            <h2 class="text-xl font-bold">{{ formattedArtists }} - {{ release.title }}</h2>
            <p class="text-600">Label: {{ formattedLabels }}</p>
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
              <li v-for="track in release.tracks" :key="track.id">
                <span>{{ track.position }}.
                  <span v-if="track.artists.length">{{ formatNames(track.artists) }} - </span>
                  {{ track.title }}
                </span>
              </li>
            </ul>
          </div>
          <div class="md:w-1/3">
            <div class="stats p-4 rounded shadow">
              <h3 class="font-semibold mb-2">Stats:</h3>
              <p><strong>Rating:</strong> {{ release.rating_average }} ({{ release.rating_count }} votes)</p>
              <p><strong>Have:</strong> {{ release.have }}</p>
              <p><strong>Want:</strong> {{ release.want }}</p>
              <p v-if="release.num_for_sale"><strong>For Sale:</strong> {{ release.num_for_sale }}</p>
              <p v-if="release.lowest_price"><strong>Lowest Price:</strong> ${{ release.lowest_price }}</p>
              <a v-if="release.kollektivx_uri" :href="release.kollektivx_uri" target="_blank" class="text-blue-500 hover:text-blue-700">
                View on KollektivX
              </a>
            </div>
          </div>
        </div>
      </div>
    </div>
  </AuthenticatedLayout>
</template>