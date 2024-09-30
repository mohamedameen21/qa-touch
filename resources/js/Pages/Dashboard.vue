<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import {Head} from '@inertiajs/vue3';
import Module from "@/Components/Module/Module.vue";
import {useModuleStore} from "@/store/module.js";
import AddModuleModal from "@/Components/Module/AddModuleModal.vue";
import {ref, useTemplateRef} from "vue";
import TestCase from "@/Components/TestCase/TestCase.vue";

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

const refreshIcon = 'https://cdn.jsdelivr.net/npm/heroicons@2.0.13/24/outline/arrow-path.svg';
</script>

<template>
    <Head title="Modules"/>
    <AddModuleModal ref="addModuleModal"/>

    <main class="grid grid-cols-1 md:grid-cols-4">
        <div id="modules" class="bg-white">
            <div class="p-5 flex flex-col gap-2 w-full h-full">
                <div class="flex justify-between items-center py-3">
                    <h2 class="">Modules</h2>
                    <div class="flex items-center gap-2">
                        <button class="btn btn-sm" @click="moduleStore.refreshModules()">
                            <img :src="refreshIcon" alt="refresh icon" class="inline-block w-6 h-6">
                        </button>
                        <button class="btn btn-outline btn-primary btn-sm inline-block"
                                @click="moduleStore.addModuleModal?.openModal()">+ Add
                        </button>
                    </div>
                </div>
                <hr>
                <Module :modules="modules" :rootId="rootId"/>
            </div>
        </div>

        <div id="test-case" class="min-h-[46rem] max-h-[46rem] col-span-3 bg-gray-100">
            <TestCase />
        </div>
    </main>

</template>
