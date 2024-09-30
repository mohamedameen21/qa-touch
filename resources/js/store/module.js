import {defineStore} from 'pinia'
import {ref} from "vue";
import axios from "axios";

export const useModuleStore = defineStore('useModuleStore', () => {
    const selectedModuleId = ref(null)
    const addModuleModal = ref(false)
    const modules = ref([])
    const moduleWithTestCases = ref(null)

    function setSelectedModule(moduleId) {
        selectedModuleId.value = moduleId
        console.log(modules.value);

        // once selected we need to make open as true for that module id
        modules.value.forEach(module => {
            if (module.id === moduleId) {
                module.open = true
            }
        });

        if (moduleId)
            fetchTestCases(moduleId);
    }

    async function refreshModules() {
        try {
            const response = await axios.post(route('modules.refresh'), {
                modules: modules.value
            });
            modules.value = response.data.data.modules;
        } catch (e) {
            console.error(e);
        }
    }

    async function fetchTestCases(moudleId) {
        try {
            const response = await axios.get(route('modules.testCase', moudleId));
            moduleWithTestCases.value = response.data.data.module;
        } catch (e) {
            console.error(e);
        }
    }

    return {
        selectedModuleId,
        addModuleModal,
        modules,
        moduleWithTestCases,
        setSelectedModule,
        refreshModules,
        fetchTestCases
    }
})
