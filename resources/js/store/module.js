import {defineStore} from 'pinia'
import {ref} from "vue";

export const useModuleStore = defineStore('useModuleStore', () => {
    const selectedModule = ref(null)
    const addModuleModal = ref(false)
    const modules = ref([])

    function setSelectedModule(module) {
        selectedModule.value = module
    }

    async function refreshModules() {
        try {
            const response = await axios.get(route('dashboard'));
            modules.value = response.data.data.modules;
        } catch (e) {
            console.error(e);
        }
    }

    return {
        selectedModule,
        setSelectedModule,
        addModuleModal,
        modules,
        refreshModules
    }
})
