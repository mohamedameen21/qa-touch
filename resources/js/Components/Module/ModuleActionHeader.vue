<script setup>
import {computed, ref, toRef} from "vue";
import axios from "axios";
import {useToast} from "vue-toastification";
import {useModuleStore} from "@/store/module.js";
import Swal from "sweetalert2";

const props = defineProps({
    moduleId: {
        type: Number,
        required: true
    },
    moduleName: {
        type: String,
        required: true
    },
    numberOfTestCase: {
        type: Number,
        required: true
    }
});

const toast = useToast();
const moduleStore = useModuleStore();

const moduleName = toRef(props, 'moduleName');
const numberOfTestCase = toRef(props, 'numberOfTestCase');

const newModuleName = ref(moduleName.value);
const canEdit = ref(false);

const displayModuleName = computed(() => {
    return numberOfTestCase.value > 0
        ? `${moduleName.value} (${numberOfTestCase.value})`
        : moduleName.value;
});

const updateModule = async () => {
    try {
        const response = await axios.put(route('modules.update', {moduleId: props.moduleId}), {
            name: newModuleName.value
        });
        canEdit.value = false;
        toast.success('Module updated successfully');
        await moduleStore.refreshModules();
        await moduleStore.fetchTestCases(moduleStore.selectedModuleId);
    } catch (e) {
        if (e?.response?.data?.message) {
            toast.error(e.response.data.message);
        } else {
            toast.error('An error occurred while adding the module');
        }
    }
};

const deleteModule = async () => {
    try {
        await Swal.fire({
            title: "Are you sure?",
            text: "The whole module including testcase will be delete",
            icon: "warning",
            showCancelButton: true,
            confirmButtonColor: "#3085d6",
            cancelButtonColor: "#d33",
            confirmButtonText: "Yes, delete it!"
        }).then(async (result) => {
            if (result.isConfirmed) {
                const response = await axios.delete(route('modules.destroy', {moduleId: props.moduleId}));
                await moduleStore.refreshModules();
                moduleStore.setSelectedModule(null);
                Swal.fire({
                    title: "Deleted!",
                    text: "Your file has been deleted.",
                    icon: "success"
                }).then(() => {
                    window.location.href = route('dashboard');
                });
            }
        });
    } catch (e) {
        console.error(e)
        toast.error('Failed to delete module');
    }
}

</script>

<template>
    <div class="p-4 text-blue-600 flex gap-2 justify-between items-center">
        <input v-if="canEdit" v-model="newModuleName" class="border-0 focus:ring-0 w-[70%] border-b">
        <h1 v-else class="my-2 font-bold text-xl">{{ displayModuleName }}</h1>
        <div class="w-64">
            <button v-if="canEdit" class="btn btn-outline btn-primary mx-3 btn-sm" @click="updateModule">Update</button>
            <button v-else class="btn btn-outline btn-ghost mx-3 btn-sm" @click="canEdit = true">Edit</button>
            <button class="btn btn-outline btn-error mx-3 btn-sm" @click="deleteModule">Delete</button>
        </div>
    </div>
</template>

<style scoped>

</style>
