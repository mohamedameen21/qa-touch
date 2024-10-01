<script setup>
import Multiselect from "@vueform/multiselect";
import {computed, ref} from "vue";
import axios from "axios";
import {useModuleStore} from "@/store/module.js";
import {useToast} from "vue-toastification";

const moduleStore = useModuleStore();
const toast = useToast();

const title = ref('');
const description = ref('');
const fileAttachment = ref(null);
const disableActionButtton = ref(false);

const errors = ref({});
const fileInputRef = ref(null);

// reactive states editing the test case
const testCaseId = ref(null);
const isModalForEdit = ref(false);
const existingFileUrl = ref(null);

const fileName = computed(() => {
    return existingFileUrl.value ? existingFileUrl.value.split('/').pop() : '';
});

const openModal = (canEdit = false, data = null) => {
    if (canEdit && data) {
        testCaseId.value = data.id;
        title.value = data.title;
        isModalForEdit.value = true;
        description.value = data.description;
        existingFileUrl.value = data.file_path;
    }

    const modal = document.getElementById('add-test-case-modal');
    modal.showModal();
}

// Expose the openModal method to the parent
defineExpose({
    openModal,
});

const closeModal = () => {
    const modal = document.getElementById('add-test-case-modal');
    modal.close();
    resetForm();
}

const addTestCase = async () => {
    try {
        const response = await axios.post(route('testCase.store', moduleStore.selectedModuleId), {
            title: title.value,
            description: description.value,
            file: fileAttachment.value,
        }, {
            headers: {
                'Content-Type': 'multipart/form-data',
            }
        });

        toast.success('Test Case Added Successfully');
        await moduleStore.fetchTestCases(moduleStore.selectedModuleId);

        return true;
    } catch (error) {
        console.error(error);
        if (error.response && error.response.data.errors) {
            errors.value = error.response.data.errors;
            return false;
        }

        toast.error('Failed to add Test Case');
        return false;
    }
}

const resetForm = () => {
    title.value = '';
    description.value = '';
    fileAttachment.value = null;
    errors.value = {};
    existingFileUrl.value = null;
    testCaseId.value = null;
    isModalForEdit.value = false;
    disableActionButtton.value = false;
    if (fileInputRef.value) {
        fileInputRef.value.value = null; // Reset the file input
    }
}

const setFile = (e) => {
    // check file size here and show error if file size is greater than 10MB
    if (e.target.files[0].size > 10000000) {
        disableActionButtton.value = true;
        toast.error('File size should not be greater than 10MB');
        return;
    }

    fileAttachment.value = e.target.files[0];
    disableActionButtton.value = false;
}

const save = async () => {
    const result = await addTestCase();
    if (result) {
        closeModal();
    }
}

const saveAndContinue = async () => {
    const result = await addTestCase();
    if (result) {
        resetForm();
    }
}

const update = async () => {
    try {
        const formData = new FormData();
        formData.append('_method', 'put');
        formData.append('title', title.value);
        formData.append('description', description.value);
        if (fileAttachment.value) {
            formData.append('file', fileAttachment.value);
        }

        const response = await axios.post(route('testCase.update', {
            moduleId: moduleStore.selectedModuleId,
            testCaseId: testCaseId.value,
        }), formData, {
            headers: {
                'Content-Type': 'multipart/form-data',
            }
        });

        toast.success('Test Case Updated Successfully');
        await moduleStore.fetchTestCases(moduleStore.selectedModuleId);
        closeModal();
    } catch (e) {
        console.error(e);
        if (e.response && e.response.data.errors) {
            errors.value = e.response.data.errors;
            return;
        }

        toast.error('Failed to update Test Case');
    }
}

</script>

<template>
    <dialog id="add-test-case-modal" class="modal">
        <div class="modal-box !min-w-[40rem] !min-h-72">
            <h3 v-if="isModalForEdit" class="text-lg font-bold mb-3">Update Test Case</h3>
            <h3 v-else class="text-lg font-bold mb-3">Add Test Case</h3>
            <!--            <div class="mt-6">-->
            <!--                <Multiselect v-model="selectedSubModule" :options="options" :searchable="true"-->
            <!--                             placeholder="Select "/>-->
            <!--            </div>-->

            <input
                v-model="title"
                id="title"
                class="inline-block mt-5 border-0 focus:ring-0 w-full border-b border-gray-300 focus:border-gray-300"
                placeholder="Enter the title"
            >
            <span v-if="errors.title" class="text-red-500">{{ errors.title[0] }}</span>

            <textarea
                v-model="description"
                class="mt-9 w-full border-gray-300 rounded-lg"
                placeholder="Enter the Description Here"
                rows="5"
            />
            <span v-if="errors.description" class="text-red-500">{{ errors.description[0] }}</span>

            <label for="file" class="block mt-5 mb-2">Additional Attachments</label>
            <div><input ref="fileInputRef" @change="setFile" type="file" class=""></div>
            <span v-if="fileName && !fileAttachment">{{ fileName }}</span>
            <span v-if="errors.file" class="text-red-500">{{ errors.file[0] }}</span>

            <div class="flex mt-7 justify-end gap-3">
                <button class="btn btn-outline" @click="closeModal">Cancel</button>
                <button :disabled="disableActionButtton" v-if="isModalForEdit" class="btn btn-outline btn-primary" @click="update">Update</button>
                <button :disabled="disableActionButtton" v-if="!isModalForEdit" class="btn btn-outline btn-primary" @click="save">Save</button>
                <button :disabled="disableActionButtton" v-if="!isModalForEdit" class="btn btn-outline btn-primary" @click="saveAndContinue">Save and
                    Continue
                </button>
            </div>
        </div>
    </dialog>
</template>

<style scoped>

</style>
