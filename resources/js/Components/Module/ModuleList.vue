<script setup>
import {useModuleStore} from "@/store/module.js";
import draggable from "vuedraggable";
import axios from "axios";

const moduleStore = useModuleStore();

defineProps({
    modules: {
        type: Array,
        required: true
    }
});

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

const handleDrag = async (event) => {
    const draggedItem = event.item;
    const fromItem = event.from;
    const toItem = event.to;

    const oldIndex = event.oldDraggableIndex;
    const newIndex = event.newDraggableIndex;
    const draggedItemId = draggedItem.getAttribute('data-module-id'); // Use getAttribute to retrieve the ID
    const oldParentId = fromItem.parentNode.getAttribute('data-parent-id');
    const newParentId = toItem.parentNode.getAttribute('data-parent-id');

    if(oldParentId === newParentId) { // Re ordering
        console.log(`Reordering item with ID ${draggedItemId} from index ${oldIndex} to index ${newIndex}`);
    } else {
        console.log(`Moving item with ID ${draggedItemId} from parent ${oldParentId} to parent ${newParentId}`);
    }

    try {
        const response = await axios.put(route('modules.update'), {
            id: draggedItemId,
            oldIndex: oldIndex,
            newIndex: newIndex,
            oldParentId: oldParentId,
            newParentId: newParentId
        });
        console.log(response.data);
    } catch (e) {
        console.error(e);
    }
}

// Icon URLs
const openIcon = 'https://cdn.jsdelivr.net/npm/heroicons@2.0.13/24/outline/folder-open.svg';
const closeIcon = 'https://cdn.jsdelivr.net/npm/heroicons@2.0.13/24/outline/folder.svg';
const chevronDownIcon = 'https://cdn.jsdelivr.net/npm/heroicons@2.0.13/24/outline/chevron-down.svg';
const chevronRightIcon = 'https://cdn.jsdelivr.net/npm/heroicons@2.0.13/24/outline/chevron-right.svg';
</script>

<template>
    <draggable
        :list="modules"
        group="modules"
        handle=".folder-handle"
        item-key="id"
        @end="handleDrag"
        class="w-full h-full"
    >
        <template #item="{ element: module }" class="w-full h-full">
            <div :data-module-id="module.id" :key="module.id" class="relative mb-3 w-full">
                <span class="absolute top-0 -left-2 w-4 h-full border-l border-gray-400"></span>

                <!-- Folder item -->
                <div
                    @click="handleClick(module)"
                    @dblclick.stop="handleDoubleClick(module)"
                    class="cursor-pointer flex shrink-0	 items-center justify-between gap-2 text-nowrap relative ml-6 text-black hover:bg-blue-200 rounded-md w-full folder-handle py-1 select-none min-w-56"
                    :class="{
                        'bg-blue-200': module.id === moduleStore.selectedModule
                    }"
                    :data-module-id="module.id"
                >
                    <div class="flex w-full shrink-0 justify-between items-center gap-2">
                        <div class="flex w-full gap-2 items-center pl-4">
                            <img
                                v-if="module.children && module.children.length > 0"
                                :src="module.open ? chevronDownIcon : chevronRightIcon"
                                alt="chevron"
                                class="w-4 h-4"
                            />
                            <img
                                v-if="module.children && module.children.length > 0"
                                :src="module.open ? openIcon : closeIcon"
                                alt="folder"
                                class="w-4 h-4"
                            />
                            <span>{{ module.name }}</span>
                        </div>
                        <button @click.stop="moduleStore.addModuleModal?.openModal(module.id)"
                                class="btn btn-xs mr-5 px-5 text-base">+
                        </button>
                    </div>
                </div>

                <!-- Nested folders -->
                <div :data-parent-id="module.id" v-if="module.open" class="ml-6 mt-2">
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
.border-l {
    border-left-width: 2px;
}
</style>
