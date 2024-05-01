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
      <h2 class="font-semibold text-xl text-gray-800 leading-tight">Release</h2>
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
              <li v-for="track in release.tracks" :key="track.id">
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
      </div>
    </div>
  </AuthenticatedLayout>
</template>