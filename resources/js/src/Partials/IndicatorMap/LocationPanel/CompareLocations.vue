<script lang="ts" setup>
import {shallowRef, ref} from 'vue';
import { fetchLocationsByType } from '../../../../services/fetch/fetch-locations';
import { useErrorStore } from '../../../../stores/errors';
import { InputIcon, Select, SelectChangeEvent } from 'primevue';
import { useIndicatorsStore } from '../../../../stores/indicators';
import { Location } from '../../../../types/locations';
import { fetchLocationIndicatorData } from '../../../../services/fetch/fetch-locations';
import {computed} from 'vue';

const indicator = useIndicatorsStore();
const errors = useErrorStore();
const comparisonIsActivated= ref<boolean>(false);


const locations = shallowRef<Location[] | null>(null);

const locationsFiltered = computed(()=>{

    if(!locations.value){
        return [];
    }
    const primaryIds = (indicator.locationIndicatorData ?? []).map(d => d.location_id);

    const comparisonIds = (indicator.comparedLocations ?? []).flat().map(d => d.location_id);

    const excludedIds = new Set([...primaryIds, ...comparisonIds]);

    return locations.value.filter(location => !excludedIds.has(location.id));

})

async function handleComparisonActivated() {

    if(!indicator.currentLocation){
        console.error("No current location selected");
        return;
    }
    comparisonIsActivated.value = !comparisonIsActivated.value;

    const {data, error} = await fetchLocationsByType(indicator.currentLocation.location_type_id);

    if(error.status){
        errors.error = true;
        errors.errorMessage = error.message;
        return;
    }

    locations.value = data;

}

async function handleLocationSelected(event:SelectChangeEvent){
    
    if(!indicator.indicator){

        console.error("No indicator selected");
        return;
    }

    const locationID = event.value.id;

    const reducedFilters = indicator.getReducedSelectedFilters('timeframe');

    const params = indicator.getFiltersAsParams(reducedFilters);

    const {data, error} = await fetchLocationIndicatorData(locationID, indicator.indicator.id, params);

    if(error.status){
        errors.error = true;
        errors.errorMessage = error.message;
        return;
    }

    indicator.updateComparedLocations(data);
}

</script>

<template>

    <section v-if="indicator.currentLocation">
        <div class="text-center">
            <h3>Compare</h3>
            <p>Add a {{ indicator.currentLocation.location_type }}</p>
        </div>
        <button 
            v-if="!comparisonIsActivated"
            @click="handleComparisonActivated"
            class="block w-fit mx-auto p-1 bg-gray-700 text-white my-3"
            >Add A Comparison</button>
        <section v-if="comparisonIsActivated && locations" class="border-2 border-gray-700 p-3">
            <Select 
                :options="locationsFiltered" 
                optionLabel="name" 
                placeholder="Select a Location"
                filter
                @change="handleLocationSelected"
                :pt="{
                    root: {
                        class: 'w-3/4 mx-auto relative p-3 rounded-lg border-2 border-gray-700'
                    },
                    dropdownIcon: {
                        class: 'absolute right-0 inset-y-1/2 -translate-y-1/2 mr-3'
                    },
                    listContainer: {
                        class: 'p-3 overflow-y-auto bg-white border-b-2 border-x-2 border-gray-700 shadow-sm'
                    },
                    option: {
                        class: 'hover:bg-gray-700 hover:text-white focus-visible:bg-gray-700 focus-visible:text-white'
                    },
                    pcFilterContainer: {
                        root: {
                            class: 'w-full relative w-3/4 mx-aut0 bg-white'
                        }
                    },
                    pcFilter: {
                        root:{
                            class: 'w-full px-3 py-1 border-2 border-gray-700 bg-gray-100'
                        }
                    },
                    pcFilterIconContainer: {
                        root: {
                            class: 'absolute right-0 inset-y-1/2 -translate-y-1/2 mr-3'
                        }
                    }

                }"
            >
            </Select>
        </section>
    </section>
</template>