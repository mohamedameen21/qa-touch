<script setup>
import axios from "axios";
import {ref} from "vue";
import Multiselect from "@vueform/multiselect";
import {useToast} from "vue-toastification";

const toast = useToast();

const selectedSubModule = ref(null);
const selectedModule = ref(null);
const options = ref([]);
const newModuleName = ref('');

const openModal = async (moduleId = null) => {
    selectedModule.value = moduleId;
    const modal = document.getElementById('add-module-modal');

    try {
        const response = moduleId ? await axios.get(route('modules.options', {moduleId: moduleId})) : await axios.get(route('modules.options'));
        newModuleName.value = '';
        selectedSubModule.value = null;

        options.value = response.data.data.modules;

        options.value = options.value.map((option) => {
            return {
                label: option.name,
                value: option.id
            };
        });


        console.log(options.value);
        modal.showModal();
    } catch (e) {
        console.error(e);
    }
}

const closeModal = () => {
    const modal = document.getElementById('add-module-modal');
    modal.close();
}

// Expose the openModal method to the parent
defineExpose({
    openModal,
});

const addModule = async () => {
    try {
        const parentId = selectedModule.value ? selectedModule.value : null; // null equals to Root

        const response = await axios.post(route('modules.store'), {
            parent_id: parentId,
            name: newModuleName.value
        });

        toast.success('Module added successfully');
        closeModal();
    } catch (e) {
        if (e?.response?.data?.message) {
            toast.error(e.response.data.message);
        } else {
            toast.error('An error occurred while adding the module');
        }
    }
}

</script>

<template>
    <dialog id="add-module-modal" class="modal">
        <div class="modal-box !min-h-72">
            <h3 class="text-lg font-bold mb-3">Add Module/Sub Module</h3>
            <div class="mt-6">
                <Multiselect v-model="selectedSubModule" :options="options" :searchable="true" placeholder="Select Sub Module"/>
            </div>

            <input v-model="newModuleName"
                   class="mt-5 border-0 focus:ring-0 w-full border-b border-gray-300 focus:border-gray-300"
                   placeholder="Enter the Module Name">

            <div class="flex mt-7 justify-end gap-3">
                <button class="btn btn-outline" @click="closeModal">Cancel</button>
                <button class="btn btn-outline btn-primary" @click="addModule">Add</button>
            </div>
        </div>
    </dialog>
</template>

<style src="@vueform/multiselect/themes/default.css"></style>

<style scoped>

</style>
