<script setup>
import ModuleList from "@/Components/Module/ModuleList.vue";
import {useModuleStore} from "@/store/module.js";

const moduleStore = useModuleStore();

const $props = defineProps({
    modules: {
        type: Array,
        required: true
    },
    rootId: {
        type: Number,
        required: true
    }
});

moduleStore.modules = $props.modules;

</script>

<template>
    <div :data-parent-id="rootId" class="p-4 overflow-scroll max-h-[40rem] w-full">
        <ModuleList :modules="moduleStore.modules"/>
    </div>
</template>

<style scoped>
/* Additional styles can be added here */
</style>
