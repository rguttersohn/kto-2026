<script lang="ts" setup>
import { useIndicatorsStore } from '../../../stores/indicators';

const indicator = useIndicatorsStore();

</script>

<template>
        <section
            class="absolute top-0 right-0 w-[33vw] h-full border-l-2 border-gray-700 bg-white"
            >
            <button 
                @click="indicator.emptyCurrentLocation"
                class="p-1 bg-gray-700 text-white">close</button>
            <h2 v-if="indicator.currentLocation"
                class="text-center text-3xl"> 
                <span class="block text-2xl">{{indicator.currentLocation.location_type}}</span> 
                {{indicator.currentLocation.location}}
            </h2>
            <ul
                class="flex h-48 gap-x-3"
                >
                <li
                    v-for="location in indicator.locationIndicatorData">
                    <div class="bg-gray-700 text-white">{{ location.timeframe }}: {{ location.data }}</div>
                </li>
            </ul>
        </section>
</template>