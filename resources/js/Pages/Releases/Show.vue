<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head } from '@inertiajs/vue3';

defineProps(['release']);
</script>

<template>
  <Head title="Release" />

  <AuthenticatedLayout>
    <template #header>
      <h2 class="font-semibold text-xl text-gray-800 leading-tight">Releases</h2>
    </template>
    <div class="py-12">
      <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
            <div class="p-6 text-gray-900">
              <v-card class="mx-auto">
                <v-img
                  :src="release.image_full_uri"
                  :alt="release.title"
                  :width="150"
                />
                <v-card-title>{{ release.artists.map(artist => artist.name).join(', ') }} - {{ release.title }}</v-card-title>
                <v-card-text>
                  <div>Label: {{ release.labels.map(label => label.name).join(', ') }} ??? CATNO ???</div>
                  <div>Format: {{ release.formats }}</div>
                  <div>Country: {{ release.country }}</div>
                  <div>Released: {{ release.released }}</div>
                  <div>Genre: {{ release.genres.map(genre => genre.name).join(', ') }}</div>
                  <div>Style: {{ release.styles.map(style => style.name).join(', ') }}</div>
                </v-card-text>
                <v-divider />
                <v-card-text>
                  <div>Rating: {{ release.rating_average }} ({{ release.rating_count }} votes)</div>
                  <div>Have: {{ release.have }}</div>
                  <div>Want: {{ release.want }}</div>
                  <div>Number for Sale: {{ release.num_for_sale }}</div>
                </v-card-text>
                <v-divider />
                <v-card-text>
                  <v-btn text :href=release.kollektivx_uri target="_blank">View on KollektivX</v-btn>
                </v-card-text>
              </v-card>
            </div>
        </div>
      </div>
    </div>
  </AuthenticatedLayout>
</template>

<style lang="scss" scoped>

</style>