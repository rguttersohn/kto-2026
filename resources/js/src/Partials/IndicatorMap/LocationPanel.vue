<script lang="ts" setup>
import { useIndicatorsStore } from '../../../stores/indicators';
import CompareLocations from './LocationPanel/CompareLocations.vue';

const indicator = useIndicatorsStore();

function handleComparisonRemove(locationID: number){
   
    indicator.removeComparedLocation(locationID);
}

</script>

<template>
    <section
        v-if="indicator.currentLocation"
        class="absolute top-0 right-0 w-[33vw] h-full p-3 border-l-2 border-gray-700 bg-white"
        >
        <button 
            @click="indicator.emptyCurrentLocation"
            class="p-1 bg-gray-700 text-white">close</button>
        <h2 
            class="text-center text-3xl"> 
            <span class="block text-2xl">{{indicator.currentLocation.location_type}}</span> 
            {{indicator.currentLocation.location}}
        </h2>
        <ul
            class="flex justify-center gap-x-3"
            >
            <li
                v-for="location in indicator.locationIndicatorData"
                :key="location.location_id"
                >
                <div class="p-1 border-2 text-gray-700">{{ location.timeframe }}: {{ location.data }}</div>
            </li>
        </ul>
        <template v-if="indicator.comparedLocations">
            <ul>
                <li 
                    v-for="comparison in indicator.comparedLocations"
                    :key="comparison[0].location_id"
                    class="relative my-3 p-3 border-2 border-gray-700 rounded-lg"
                    >
                    <button 
                        @click="handleComparisonRemove(comparison[0].location_id)"
                        class="absolute left-0 top-0 p-1 bg-gray-700 text-white">remove</button>
                    <h3 class="text-center">{{ comparison[0].location }}</h3>
                    <ul class="flex justify-center gap-x-3" >
                        <li v-for="location in comparison"
                            :key="location.location_id"
                        >
                        <div class="p-1 border-2 text-gray-700">{{ location.timeframe }}: {{ location.data }}</div>
                        </li>
                    </ul>
                </li>
            </ul>
        </template>
        <CompareLocations/>
    </section>
</template>