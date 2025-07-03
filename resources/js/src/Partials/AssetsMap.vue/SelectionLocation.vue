<script lang="ts" setup>
import { Select, SelectChangeEvent } from 'primevue';
import { useAssetsStore } from '../../../stores/assets';

const asset = useAssetsStore();

async function handleLocatonSelected(event: SelectChangeEvent){
    console.log(event.value);
}

</script>

<template>
    <template v-if="asset.assetLocationTypes">
        <Select
            :options="asset.assetLocationTypes"
            optionLabel="name"
            placeholder="Location Type"
            @change="handleLocatonSelected"
            :pt="{
                root: {
                    class: 'relative p-3 rounded-lg border-2 border-gray-700',
                },
                dropdownIcon: {
                    class: 'absolute right-0 inset-y-1/2 -translate-y-1/2 mr-3',
                },
                listContainer: {
                    class: 'p-3 overflow-y-auto bg-white border-b-2 border-x-2 border-gray-700 shadow-sm',
                },
                option: {
                    class: 'hover:bg-gray-700 hover:text-white focus-visible:bg-gray-700 focus-visible:text-white',
                },
            }"
        >
            <template #value>
                Select a Location Type
            </template>
        </Select>
    </template>
</template>