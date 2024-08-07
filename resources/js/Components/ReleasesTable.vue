<script setup>
import { ref, onMounted } from 'vue';
import { router, Link } from '@inertiajs/vue3'

const props = defineProps({
  releases: {
    type: Object,
    required: true,
  },
});

const searchOption = ref(null);
const query = ref('');
const searchOptions = [
  { text: 'By Artist', value: 'artist' },
  { text: 'By Title', value: 'title' },
  { text: 'By Artist - Title', value: 'artist-title' },
  { text: 'By Label', value: 'label' },
  { text: 'By Country', value: 'country' },
  { text: 'By Genre', value: 'genre' },
  { text: 'By Style', value: 'style' },
];

const handleSearchOptionChange = () => {
  query.value = '';
};

const loading = ref(false);

const headers = ref([
  { title: 'Cover', key: 'image', sortable: false },
  { title: 'Actions', key: 'actions', sortable: false },
  { title: 'Artists', key: 'artists' },
  { title: 'Title', key: 'title' },
  { title: 'Labels', key: 'labels' },
  { title: 'Formats', key: 'formats' },
  { title: 'Country', key: 'country' },
  { title: 'Released', key: 'released' },
  { title: 'Year', key: 'year' },
  { title: 'Genres', key: 'genres' },
  { title: 'Styles', key: 'styles' },
  { title: 'Avg Rating', key: 'rating_average' },
  { title: 'Ratings', key: 'rating_count' },
  { title: 'Have', key: 'have' },
  { title: 'Want', key: 'want' },
  { title: 'In Stock', key: 'num_for_sale' },
  { title: 'Lowest Price', key: 'lowest_price' },
]);

// Getting the current path dynamically
const currentPath = new URL(window.location.href).pathname;

// function loadReleases({ page, itemsPerPage, sortBy }) { // TODO: sortBy
//   loading.value = true;

//   const params = {
//     per_page: itemsPerPage,
//     page: page,
//   };

//   router.get(currentPath, params, {
//     preserveState: true,
//     preserveScroll: true,
//     onSuccess: (page) => {
//       props.releases.data = page.props.releases?.data;
//     },
//     onFinish: () => {
//       loading.value = false;
//     }
//   });
// }

function loadReleases({ page, itemsPerPage, sortBy }) { // TODO: sortBy
  loading.value = true;

  const params = {
    per_page: itemsPerPage,
    page: page,
    query: query.value,
    search_option: searchOption.value,
  };

  router.get(currentPath, params, {
    preserveState: true,
    preserveScroll: true,
    onSuccess: (page) => {
      props.releases.data = page.props.releases?.data;
    },
    onFinish: () => {
      loading.value = false;
    }
  });
}

onMounted(() => {
  const urlParams = new URLSearchParams(window.location.search);
  const page = parseInt(urlParams.get('page') || '1');
  const perPage = parseInt(urlParams.get('per_page') || '10');

  loadReleases({ page, itemsPerPage: perPage });
});
</script>

<template>
  <v-container>
    <v-row class="d-flex justify-center align-center">
      <v-col cols="6" md="4">
        <v-select
          v-model="searchOption"
          :items="searchOptions"
          label="Select Search Option"
          item-title="text"
          item-value="value"
          @update:model-value="handleSearchOptionChange"
          clearable
          chips
          density="compact"
          hide-details
          variant="outlined"
        />
      </v-col>
      <v-col cols="6" md="6">
        <v-text-field
          v-model="query"
          :disabled="!searchOption"
          placeholder="Type your search query"
          @input="loadReleases"
          density="compact"
          hide-details
          variant="outlined"
        />
      </v-col>
    </v-row>
  </v-container>
  <v-data-table-server
    v-model:items-per-page="releases.per_page"
    :headers="headers"
    :items="releases.data"
    :items-length="releases.total ?? 0"
    :page="releases.current_page"
    :loading="loading"
    @update:options="loadReleases"
    color="success"
  >
    <template v-slot:item.image="{ item }">
      <a
        :href="item.uri"
        target="_blank"
        >
          <img :src="item.image_thumbnail_uri" alt="Cover" class="w-16 object-cover">
      </a>
    </template>

    <template v-slot:item.actions="{ item }">
      <Link :href="route('releases.show', item.id)">
        <v-icon
          class="me-2"
          size="small"
          color="primary"
        >
          mdi-eye
        </v-icon>
      </Link>
    </template>

    <template v-slot:item.artists="{ item }">
      <span>
        <a
          v-for="(artist, index) in item.artists"
          :key="artist.id"
          :href="`https://www.discogs.com/artist/${artist.discogs_id}`"
          class="hover:underline"
          target="_blank"
          >
          {{ artist.name }}
          <template v-if="index !== item.artists.length - 1 && item.artists.length > 1"> , </template>
        </a>
      </span>
    </template>

    <template v-slot:item.title="{ item }">
      <span>
        <a
          :href="item.uri"
          class="hover:underline"
          target="_blank"
          >
          {{ item.title }}
        </a>
      </span>
    </template>

    <template v-slot:item.labels="{ item }">
      <span>
        <a
          v-for="(label, index) in item.labels"
          :key="label.id"
          :href="`https://www.discogs.com/label/${label.discogs_id}`"
          class="hover:underline"
          target="_blank"
          >
          {{ label.name }}
          <template v-if="index !== item.labels.length - 1 && item.labels.length > 1"> , </template>
        </a>
      </span>
    </template>

    <template v-slot:item.genres="{ item }">
      <span v-if="item.genres.length">
        {{ item.genres.map(genre => genre.name).join(', ') }}
      </span>
    </template>

    <template v-slot:item.styles="{ item }">
      <span v-if="item.styles.length">
        {{ item.styles.map(style => style.name).join(', ') }}
      </span>
    </template>

    <template v-slot:item.lowest_price="{ item }">
      {{ item.lowest_price !== null && item.lowest_price !== 0 ? '$' + item.lowest_price.toFixed(2) : 'N/A' }}
    </template>
  </v-data-table-server>
</template>