<script setup lang="ts">
import AppLayout from '../Layouts/AppLayout.vue';
import { usePage } from '@inertiajs/vue3';
import {watch} from 'vue';
import { Domain } from '../../types/well-being';
import { LocationType } from '../../types/locations';
import { Select } from 'primevue';
import type { SelectChangeEvent } from 'primevue';
import { useWellBeingStore } from '../../stores/well-being';

defineOptions({
    layout: AppLayout
});

const page = usePage<{
    domains: Domain[],
    location_types: LocationType[]
}>();

const wellBeing = useWellBeingStore();


function handleLocationTypeSelected(event: SelectChangeEvent){

    wellBeing.currentLocationType = event.value;

}

function handleDomainSelected(event: SelectChangeEvent){
    
    wellBeing.currentDomain = event.value;

}

watch([() => wellBeing.currentDomain, () => wellBeing.currentLocationType], () => {

    if(!wellBeing.currentDomain || !wellBeing.currentLocationType) {
        return;
    }

    

});

</script>

<template>
    <section class="w-10/12 mx-auto">
        <h1>Well Being Index</h1>
        <section class="flex flex-col gap-y-10 my-20 w-1/4 border-2 border-gray-700 p-5 rounded-lg">
            <h2>Select Location Type and Domain</h2>
            <Select
                :options="page.props.location_types"
                option-label="name"
                placeholder="Select a Location Type"
                @change="handleLocationTypeSelected"
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
            ></Select>
            <Select
                :options="page.props.domains"
                option-label="name"
                placeholder="Select a Domain"
                @change="handleDomainSelected"
                :pt="{
                        root: {
                            class: 'relative p-3 rounded-lg border-2 border-gray-700',
                        },
                        dropdownIcon: {
                            class: 'absolute right-0 inset-y-1/2 -translate-y-1/2 mr-3',
                        },
                        listContainer: {
                            class: 'p-3 ovserflow-y-auto bg-white border-b-2 border-x-2 border-gray-700 shadow-sm',
                        },
                        option: {
                            class: 'hover:bg-gray-700 hover:text-white focus-visible:bg-gray-700 focus-visible:text-white',
                        },
                    }"
            ></Select>
        </section>
        <section v-if="wellBeing.currentDomain?.name && wellBeing.currentLocationType" class="w-10/12 mx-auto">
            <h2>{{ wellBeing.currentDomain?.name }} in {{ wellBeing.currentLocationType?.plural_name }}</h2>
        </section>
    </section>
</template>