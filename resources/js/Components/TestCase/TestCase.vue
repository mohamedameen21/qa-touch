<script setup>
import {useModuleStore} from "@/store/module.js";
import {storeToRefs} from "pinia";
import ModuleActionHeader from "@/Components/Module/ModuleActionHeader.vue";
import {ref, useTemplateRef} from "vue";
import TestCaseActionModal from "@/Components/TestCase/TestCaseActionModal.vue";
import axios from "axios";
import {useToast} from "vue-toastification";

const moduleStore = useModuleStore();
const toast = useToast();

const {moduleWithTestCases} = storeToRefs(moduleStore);
const addTestCaseModal = useTemplateRef('addTestCaseModal');

const pencilIcon = 'https://cdn.jsdelivr.net/npm/heroicons@2.0.13/24/outline/pencil.svg';
const trashIcon = 'https://cdn.jsdelivr.net/npm/heroicons@2.0.13/24/outline/trash.svg';
const checkCircle = 'https://cdn.jsdelivr.net/npm/heroicons@2.0.13/24/outline/check-circle.svg';
const xCircle = 'https://cdn.jsdelivr.net/npm/heroicons@2.0.13/24/outline/x-circle.svg';

const resetModuleWithTestCases = () => {
    moduleStore.moduleWithTestCases.value = null;
};

const deleteTestCase = async (testCaseId) => {
    try {
        await axios.delete(route('testCase.destroy', {
            moduleId: moduleStore.selectedModuleId,
            testCaseId: testCaseId
        }));
        toast.success('Test case deleted successfully');
        moduleStore.fetchTestCases(moduleStore.selectedModuleId);
    } catch (e) {
        console.error(e);
        toast.error('Failed to delete test case');
    }
};

</script>

<template>
    <div v-if="moduleWithTestCases" class="h-full w-full">
        <TestCaseActionModal ref="addTestCaseModal"/>

        <ModuleActionHeader
            :moduleId="moduleWithTestCases.id"
            :moduleName="`${moduleWithTestCases.name}`"
            :numberOfTestCase="moduleWithTestCases.test_cases.length"
        />
        <hr class="my-2">

        <div class="mt-5 w-11/12 m-auto flex justify-between items-center">
            <h3 class="">Test Cases</h3>
            <button class="btn btn-primary btn-sm" @click="addTestCaseModal.openModal()">+ Add Test Case</button>
        </div>

        <div class="my-3 w-11/12 m-auto max-h-[70%] overflow-scroll">
            <div class="overflow-x-auto bg-white rounded-lg">
                <table class="table table-pin-rows">
                    <!-- head -->
                    <thead>
                    <tr>
                        <th></th>
                        <th>Ticket No.</th>
                        <th>Title</th>
                        <th>File</th>
                        <th></th>
                    </tr>
                    </thead>
                    <tbody>
                    <!-- row 1 -->
                    <tr v-for="(testCase, index) in moduleWithTestCases.test_cases">
                        <th>{{ index + 1 }}</th>
                        <td>
                            <div class="badge badge-primary badge">{{ testCase.ticket_no }}</div>
                        </td>
                        <td class="text-truncate">{{ testCase.title }}</td>
                        <td>
                            <img :src="testCase.file_path ? checkCircle : xCircle" alt="file upload icon"
                                 class="w-4 h-4"/>
                        </td>
                        <td>
                            <div class="flex justify-between">
                                <button class="py-2"><img :src="pencilIcon" alt="edit icon" class="w-4 h-4"
                                                          @click="addTestCaseModal?.openModal(true, testCase)"/></button>
                                <button class="py-2"><img :src="trashIcon" alt="delete icon" class="w-4 h-4" @click="deleteTestCase(testCase.id)" /></button>
                            </div>
                        </td>
                    </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <div v-else>
        <h1 class="text-center text-2xl">No Module Selected</h1>
    </div>

</template>

<style scoped>
</style>
