<script lang="ts" setup>
import { Select, SelectChangeEvent } from 'primevue';
import { usePage } from '@inertiajs/vue3';
import { Domain } from '../../../../types/well-being';
import { useWellBeingStore } from '../../../../stores/well-being';
import { Location } from '../../../../types/locations';
import { fetchLocationWellBeing } from '../../../../services/fetch/fetch-locations';

const page = usePage<{
    well_being_domains: Array<Domain>,
    location: Location
}>();

const wellBeing = useWellBeingStore();

async function handleDomainSelection(event:SelectChangeEvent){

    wellBeing.currentDomain = event.value;

    const params = 'filter[domain][eq]='+wellBeing.currentDomain?.id;

    const {data, error} = await fetchLocationWellBeing(page.props.location.id, params);

    console.log(data);

}


</script>

<template>
    <Select
        :options="page.props.well_being_domains"
        option-label="name"
        @change="handleDomainSelection"
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
        <template v-slot:value>{{ wellBeing.currentDomain?.name ?? 'Select a Domain' }}</template>
    </Select>
</template>