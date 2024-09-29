<script setup>
import {useModuleStore} from "@/store/module.js";
import draggable from "vuedraggable";

const moduleStore = useModuleStore();

defineProps({
    modules: {
        type: Array,
        required: true
    }
});

// Toggle folder open/close
const toggleFolder = (folder) => {
    folder.open = !folder.open;
};

const handleDoubleClick = (folder) => {
    folder.open = true;
    moduleStore.setSelectedModule(folder.id);
};

let clickTimeout = null;

const handleClick = (folder) => {
    if (clickTimeout) {
        clearTimeout(clickTimeout);
        clickTimeout = null;
    } else {
        clickTimeout = setTimeout(() => {
            toggleFolder(folder); // Single click: toggle folder open/close
            clickTimeout = null;
        }, 200); // Delay for double-click detection
    }
};

// Icon URLs
const openIcon = 'https://cdn.jsdelivr.net/npm/heroicons@2.0.13/24/outline/folder-open.svg';
const closeIcon = 'https://cdn.jsdelivr.net/npm/heroicons@2.0.13/24/outline/folder.svg';
const chevronDownIcon = 'https://cdn.jsdelivr.net/npm/heroicons@2.0.13/24/outline/chevron-down.svg';
const chevronRightIcon = 'https://cdn.jsdelivr.net/npm/heroicons@2.0.13/24/outline/chevron-right.svg';
</script>

<template>
    <draggable :list="modules" group="modules" handle=".folder-handle" item-key="id">
        <template #item="{ element: module }">
            <div :key="module.id" class="relative mb-3 w-full">
                <span class="absolute top-0 -left-2 w-4 h-full border-l border-gray-400"></span>

                <!-- Folder item -->
                <div
                    @click="handleClick(module)"
                    @dblclick.stop="handleDoubleClick(module)"
                    class="cursor-pointer flex items-center justify-between gap-2 text-nowrap relative ml-6 text-black hover:bg-blue-200 rounded-md w-full folder-handle py-1 select-none"
                    :class="{
                        'bg-blue-200': module.id === moduleStore.selectedModule
                    }"
                >
                    <div class="flex w-full justify-between items-center gap-2">
                        <div class="flex gap-2 items-center">
                            <img
                                v-if="module.children && module.children.length > 0"
                                :src="module.open ? openIcon : closeIcon"
                                alt="folder"
                                class="w-4 h-4"
                            />
                            <span>{{ module.name }}</span>
                            <img
                                v-if="module.children && module.children.length > 0"
                                :src="module.open ? chevronDownIcon : chevronRightIcon"
                                alt="chevron"
                                class="w-4 h-4"
                            />
                        </div>
                        <button @click.stop="moduleStore.addModuleModal?.openModal(module.id)" class="btn btn-xs mr-5 px-5 text-base">+</button>
                    </div>
                </div>

                <!-- Nested folders -->
                <div v-if="module.open" class="ml-6 mt-2">
                    <ModuleList
                        v-if="module.children && module.children.length > 0"
                        :modules="module.children"
                    />
                </div>
            </div>
        </template>
    </draggable>
</template>


<style scoped>
/* Styling for the vertical connector */
.border-l {
    border-left-width: 2px;
}
</style>



<!--<template>-->
<!--    <draggable :list="modules" group="modules" handle=".folder-handle" item-key="id">-->
<!--        <template #item="{ element: module }">-->
<!--            <div :key="module.id" class="relative mb-3 w-full">-->
<!--                <span class="absolute top-0 -left-2 w-4 h-full border-l border-gray-400"></span>-->

<!--                &lt;!&ndash; Folder item &ndash;&gt;-->
<!--                <div-->
<!--                    @click="handleClick(module)"-->
<!--                    @dblclick.stop="handleDoubleClick(module)"-->
<!--                    class="cursor-pointer flex items-center justify-between gap-2 text-nowrap relative ml-6 text-black hover:bg-blue-200 rounded-md w-full folder-handle py-1"-->
<!--                    :class="{-->
<!--                        'bg-blue-200': module.id === moduleStore.selectedModule-->
<!--                    }"-->
<!--                >-->
<!--                    <div class="flex w-full justify-between items-center gap-2">-->
<!--                        <div class="flex gap-2 items-center">-->
<!--                            <img-->
<!--                                v-if="module.children && module.children.length > 0"-->
<!--                                :src="module.open ? openIcon : closeIcon"-->
<!--                                alt="folder"-->
<!--                                class="w-4 h-4"-->
<!--                            />-->
<!--                            <span>{{ module.name }}</span>-->
<!--                            <img-->
<!--                                v-if="module.children && module.children.length > 0"-->
<!--                                :src="module.open ? chevronDownIcon : chevronRightIcon"-->
<!--                                alt="chevron"-->
<!--                                class="w-4 h-4"-->
<!--                            />-->
<!--                        </div>-->
<!--                        <button @click.stop="moduleStore.addModuleModal?.openModal(module.id)" class="btn btn-xs mr-5 px-5 text-base">+</button>-->
<!--                    </div>-->
<!--                </div>-->

<!--                &lt;!&ndash; Nested folders &ndash;&gt;-->
<!--                <div v-if="module.open" class="ml-6 mt-2">-->
<!--                    <ModuleList-->
<!--                        v-if="module.children && module.children.length > 0"-->
<!--                        :modules="module.children"-->
<!--                    />-->
<!--                </div>-->
<!--            </div>-->
<!--        </template>-->
<!--    </draggable>-->
<!--</template>-->

<!--<script setup>-->
<!--import { useModuleStore } from "@/store/module.js";-->
<!--import draggable from "vuedraggable";-->

<!--const moduleStore = useModuleStore();-->

<!--defineProps({-->
<!--    modules: {-->
<!--        type: Array,-->
<!--        required: true-->
<!--    }-->
<!--});-->

<!--// Folder open/close logic-->
<!--const toggleFolder = (folder) => {-->
<!--    folder.open = !folder.open;-->
<!--};-->

<!--const handleDoubleClick = (folder) => {-->
<!--    folder.open = true;-->
<!--    moduleStore.setSelectedModule(folder.id);-->
<!--};-->

<!--let clickTimeout = null;-->

<!--const handleClick = (folder) => {-->
<!--    if (clickTimeout) {-->
<!--        clearTimeout(clickTimeout);-->
<!--        clickTimeout = null;-->
<!--    } else {-->
<!--        clickTimeout = setTimeout(() => {-->
<!--            toggleFolder(folder);-->
<!--            clickTimeout = null;-->
<!--        }, 200);-->
<!--    }-->
<!--};-->

<!--// Icons-->
<!--const openIcon = 'https://cdn.jsdelivr.net/npm/heroicons@2.0.13/24/outline/folder-open.svg';-->
<!--const closeIcon = 'https://cdn.jsdelivr.net/npm/heroicons@2.0.13/24/outline/folder.svg';-->
<!--const chevronDownIcon = 'https://cdn.jsdelivr.net/npm/heroicons@2.0.13/24/outline/chevron-down.svg';-->
<!--const chevronRightIcon = 'https://cdn.jsdelivr.net/npm/heroicons@2.0.13/24/outline/chevron-right.svg';-->
<!--</script>-->
