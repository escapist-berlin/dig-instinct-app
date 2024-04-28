<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import ReleasesTable from '@/Components/ReleasesTable.vue'
import { Head } from '@inertiajs/vue3';
import { usePage } from '@inertiajs/vue3';

const { props } = usePage();
const releases = props.releases;
</script>

<template>
    <Head title="Dashboard" />

    <AuthenticatedLayout>
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">Dashboard</h2>
        </template>

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                  <ReleasesTable
                    :tableTitle="'KollektivX Records'"
                    :releases="releases"
                    />
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
