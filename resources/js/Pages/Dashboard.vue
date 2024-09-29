<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import {Head} from '@inertiajs/vue3';
import Module from "@/Components/Module.vue";
import {useModuleStore} from "@/store/module.js";
import AddModuleModal from "@/Components/Module/AddModuleModal.vue";
import {ref, useTemplateRef} from "vue";
import {storeToRefs} from "pinia";

defineOptions({layout: AuthenticatedLayout});

const moduleStore = useModuleStore();
moduleStore.addModuleModal = useTemplateRef('addModuleModal')

const props = defineProps({
    modules: {
        type: Array,
        required: true
    },
    rootId: {
        type: Number,
        required: true
    }
});

</script>

<template>
    <Head title="Modules"/>
    <AddModuleModal ref="addModuleModal"/>

    <main class="grid grid-cols-1 md:grid-cols-4">
        <div id="modules" class="bg-white">
            <div class="p-5 flex flex-col gap-2 w-full">
                <div class="flex justify-between items-center">
                    <h2 class="">Modules</h2>
                    <button class="btn btn-outline btn-primary btn-sm" @click="moduleStore.addModuleModal?.openModal()">+ Add</button>
                </div>
                <hr>
                <Module :folders="modules" :rootId="rootId"/>
            </div>
        </div>
        <div id="test-case" class="min-h-screen col-span-3">
            <h1>Test Case</h1>
            {{ moduleStore.selectedModule }}
        </div>

    </main>

</template>
