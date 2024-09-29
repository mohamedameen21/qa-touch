<script setup>
import {ref} from 'vue';
import ModuleList from "@/Components/Module/ModuleList.vue";

const $props = defineProps({
    folders: {
        type: Array,
        required: true
    },
    rootId: {
        type: Number,
        required: true
    }
});

const folders = ref($props.folders);

</script>

<template>
    <div :data-parent-id="rootId" class="p-4 overflow-scroll min-h-screen max-h-screen w-full">
        <ModuleList :modules="folders"/>
    </div>
</template>

<style scoped>
/* Additional styles can be added here */
</style>
