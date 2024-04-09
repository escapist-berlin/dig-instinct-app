<script>
  export default {
    data: () => ({
      dialog: false,
      dialogDelete: false,
      editedIndex: -1,
      editedItem: {
        name: '',
        calories: 0,
        fat: 0,
        carbs: 0,
        protein: 0,
      },
      defaultItem: {
        name: '',
        calories: 0,
        fat: 0,
        carbs: 0,
        protein: 0,
      },
    }),

    computed: {
      formTitle () {
        return this.editedIndex === -1 ? 'New Item' : 'Edit Item'
      },
    },

    watch: {
      dialog (val) {
        val || this.close()
      },
      dialogDelete (val) {
        val || this.closeDelete()
      },
    },

    methods: {
      editItem (item) {
        this.editedIndex = this.desserts.indexOf(item)
        this.editedItem = Object.assign({}, item)
        this.dialog = true
      },

      deleteItem (item) {
        this.editedIndex = this.desserts.indexOf(item)
        this.editedItem = Object.assign({}, item)
        this.dialogDelete = true
      },

      deleteItemConfirm () {
        this.desserts.splice(this.editedIndex, 1)
        this.closeDelete()
      },

      close () {
        this.dialog = false
        this.$nextTick(() => {
          this.editedItem = Object.assign({}, this.defaultItem)
          this.editedIndex = -1
        })
      },

      closeDelete () {
        this.dialogDelete = false
        this.$nextTick(() => {
          this.editedItem = Object.assign({}, this.defaultItem)
          this.editedIndex = -1
        })
      },

      save () {
        if (this.editedIndex > -1) {
          Object.assign(this.desserts[this.editedIndex], this.editedItem)
        } else {
          this.desserts.push(this.editedItem)
        }
        this.close()
      },
    },
  }
</script>

<script setup>
import { defineProps, ref } from 'vue';

const props = defineProps({
  tableTitle: {
    type: String,
    required: true,
    default: "Releases"
  }
});

const headers = ref([
  { title: 'Cover', key: 'images[0].resource_url', sortable: false }, // TODO
  { title: 'Artists', key: 'artists' },
  { title: 'Country', key: 'country' },
  { title: 'Formats', key: 'formats' },
  { title: 'Labels', key: 'labels' }, // TODO: Test to check if a label has (1) or (2) etc. & for other properties too?
  { title: 'Released', key: 'released_formatted' },
  { title: 'Year', key: 'year' },
  { title: 'Styles', key: 'styles' },
  { title: 'Avg Rating', key: 'community.rating.average' },
  { title: 'Ratings', key: 'community.rating.count' },
  { title: 'Have', key: 'community.have' },
  { title: 'Want', key: 'community.want' },
  { title: 'In Stock', key: 'num_for_sale' },
  { title: 'Lowest Price', key: 'lowest_price' },
  { title: 'Discogs URL', key: 'uri' },
  { title: 'Actions', key: 'actions', sortable: false },
]);

const releases = ref([]);
// API Call for one release - DELETE IT LATER
const releaseIds = [1288869, 25656, 611078, 1302667, 198947];

releaseIds.forEach(releaseId => {
  const apiUrl = `https://api.discogs.com/releases/${releaseId}`;

  fetch(apiUrl)
    .then(response => {
      if (!response.ok) {
        throw new Error(`Network response for release ${releaseId} was not ok`);
      }
      return response.json();
    })
    .then(data => {
      // console.log(data);
      releases.value.push(data);
    })
    .catch(error => {
      console.log(`There was a problem with the fetch operation for release ${releaseId}:`, error.message);
    });
});
</script>

<template>
  <v-data-table
    :headers="headers"
    :items="releases"
    :sort-by="[{ key: 'year', order: 'asc' }]"
  >
    <template v-slot:top>
      <v-toolbar
        flat
      >
        <v-toolbar-title>{{ tableTitle }}</v-toolbar-title>
        <v-divider
          class="mx-4"
          inset
          vertical
        ></v-divider>
        <v-spacer></v-spacer>
        <v-dialog
          v-model="dialog"
          max-width="500px"
        >
          <template v-slot:activator="{ props }">
            <v-btn
              class="mb-2"
              color="primary"
              dark
              v-bind="props"
            >
              New Item
            </v-btn>
          </template>
          <v-card>
            <v-card-title>
              <span class="text-h5">{{ formTitle }}</span>
            </v-card-title>

            <v-card-text>
              <v-container>
                <v-row>
                  <v-col
                    cols="12"
                    md="4"
                    sm="6"
                  >
                    <v-text-field
                      v-model="editedItem.name"
                      label="Dessert name"
                    ></v-text-field>
                  </v-col>
                  <v-col
                    cols="12"
                    md="4"
                    sm="6"
                  >
                    <v-text-field
                      v-model="editedItem.calories"
                      label="Calories"
                    ></v-text-field>
                  </v-col>
                  <v-col
                    cols="12"
                    md="4"
                    sm="6"
                  >
                    <v-text-field
                      v-model="editedItem.fat"
                      label="Fat (g)"
                    ></v-text-field>
                  </v-col>
                  <v-col
                    cols="12"
                    md="4"
                    sm="6"
                  >
                    <v-text-field
                      v-model="editedItem.carbs"
                      label="Carbs (g)"
                    ></v-text-field>
                  </v-col>
                  <v-col
                    cols="12"
                    md="4"
                    sm="6"
                  >
                    <v-text-field
                      v-model="editedItem.protein"
                      label="Protein (g)"
                    ></v-text-field>
                  </v-col>
                </v-row>
              </v-container>
            </v-card-text>

            <v-card-actions>
              <v-spacer></v-spacer>
              <v-btn
                color="blue-darken-1"
                variant="text"
                @click="close"
              >
                Cancel
              </v-btn>
              <v-btn
                color="blue-darken-1"
                variant="text"
                @click="save"
              >
                Save
              </v-btn>
            </v-card-actions>
          </v-card>
        </v-dialog>
        <v-dialog v-model="dialogDelete" max-width="500px">
          <v-card>
            <v-card-title class="text-h5">Are you sure you want to delete this item?</v-card-title>
            <v-card-actions>
              <v-spacer></v-spacer>
              <v-btn color="blue-darken-1" variant="text" @click="closeDelete">Cancel</v-btn>
              <v-btn color="blue-darken-1" variant="text" @click="deleteItemConfirm">OK</v-btn>
              <v-spacer></v-spacer>
            </v-card-actions>
          </v-card>
        </v-dialog>
      </v-toolbar>
    </template>

    <template v-slot:item.artists="{ item }">
      <span>
        <a
          v-for="(artist, index) in item.artists"
          :key="artist.id"
          :href="`https://www.discogs.com/artist/${artist.id}`"
          target="_blank"
        >
          {{ artist.name }}
          <template v-if="index !== item.artists.length - 1 && item.artists.length > 1"> , </template>
        </a>
      </span>
    </template>

    <template v-slot:item.formats="{ item }">
      {{ item.formats.map(format => `${format.name}, ${format.descriptions.join(', ')}`).join(' ') }}
    </template>

    <template v-slot:item.labels="{ item }">
      <span>
        <a
          v-for="(label, index) in item.labels"
          :key="label.id"
          :href="`https://www.discogs.com/label/${label.id}`"
          target="_blank"
        >
          {{ label.name }}
          <template v-if="index !== item.labels.length - 1 && item.labels.length > 1">, </template>
        </a>
      </span>
    </template>

    <template v-slot:item.styles="{ item }">
      {{ item.styles.join(', ') }}
    </template>

    <template v-slot:item.lowest_price="{ item }">
      {{ item.lowest_price !== null && item.lowest_price !== 0 ? '$' + item.lowest_price.toFixed(2) : 'N/A' }}
    </template>

    <template v-slot:item.actions="{ item }">
      <v-icon
        class="me-2"
        size="small"
        @click="editItem(item)"
      >
        mdi-pencil
      </v-icon>
      <v-icon
        size="small"
        @click="deleteItem(item)"
      >
        mdi-delete
      </v-icon>
    </template>
    <template v-slot:no-data></template>
  </v-data-table>
</template>