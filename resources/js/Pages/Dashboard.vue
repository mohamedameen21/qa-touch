<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head } from '@inertiajs/vue3';
defineOptions({ layout: AuthenticatedLayout });

</script>

<template>
    <Head title="Modules" />

    <main class="grid grid-cols-1 md:grid-cols-3">
        <div id="modules" class="m-h-20 bg-red-500">
            <h1>Modules</h1>
        </div>

        <div id="test-case" class="m-h-20 col-span-2 bg-yellow-500">
            <h1>Test Case</h1>
        </div>

    </main>

</template>
