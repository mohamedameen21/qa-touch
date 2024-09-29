import {defineStore} from 'pinia'
import {ref} from "vue";

export const useModuleStore = defineStore('useModuleStore', () => {
    const selectedModule = ref(null)
    const addModuleModal = ref(false)

    function setSelectedModule(module) {
        selectedModule.value = module
    }

    return {
        selectedModule,
        setSelectedModule,
        addModuleModal
    }
})
