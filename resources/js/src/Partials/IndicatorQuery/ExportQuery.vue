<script lang="ts" setup>
import { computed, ref } from 'vue';
import { useIndicatorsStore } from '../../../stores/indicators';
import { Select } from 'primevue';

const indicator = useIndicatorsStore();

const filters = computed(()=>indicator.getFiltersAsParams(indicator.selectedFilters));

const options = ref<{
    value: string,
    label: string
}[]>([
    {
        value: 'json',
        label: 'JSON'
    },
    {
        value: 'geojson',
        label: 'GeoJSON'
    },
    {
        value: 'csv',
        label: 'CSV'
    }
]);

const currentOption = ref<{
    value: string,
    label: string
}>({
    value: 'json',
    label: 'JSON'
})


</script>

<template>
    <section class="flex justify-end w-10/12 mx-auto my-10 h-10">
        <div class="flex items-center h-3">
            <a  
                v-if="indicator.indicator"
                :href="`/api/app/indicators/${indicator.indicator.id}/data/export?${filters}&as=${currentOption.value}`"
                class="p-3 border-2 border-gray-700 bg-gray-700 text-white text-xl"
                >
                Export As:
            </a>
            <Select
                :options="options"
                optionLabel="label"
                @change="(event)=>currentOption = event.value"
                :pt="{
                    root: {
                        class: 'relative w-24 p-3 border-2 border-gray-700 text-gray-700'
                    },
                    dropDownIcon: {
                        class: 'absolute right-0 inset-y-1/2 -translate-y-1/2 mr-3'
                    },
                    label: {
                        class: 'text-xl'
                    },
                    listContainer: {
                        class: 'p-3 border-2 border-gray-700 bg-white'
                    },
                    option: {
                        class: 'hover:bg-gray-700 hover:text-white focus-visible:bg-gray-700 focus-visible:text-white'
                    }

                }"
            >
                <template v-slot:value>
                    {{ currentOption.label }}
                </template>
            </Select>
        </div>
    </section>
</template>