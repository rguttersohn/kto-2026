<script lang="ts" setup>
import { useIndicatorsStore } from '../../../stores/indicators';
import { ref } from 'vue';
import { Select, SelectChangeEvent } from 'primevue';

const indicator = useIndicatorsStore();

const comparisonIsActivated= ref<boolean>(false);

function handleComparisonActivated() {
    comparisonIsActivated.value = !comparisonIsActivated.value;
}

const locations = [
        {
        label: 'New York',
        value: 1
        }
    ,{
        label: 'Los Angeles',
        value: 2},
    {
        label: 'Chicago',
        value: 3
    }
]

function handleLocationSelected(event:SelectChangeEvent){
    console.log(event.value);
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
                class="flex h-48 gap-x-3"
                >
                <li
                    v-for="location in indicator.locationIndicatorData">
                    <div class="bg-gray-700 text-white">{{ location.timeframe }}: {{ location.data }}</div>
                </li>
            </ul>
            <section>
                <div class="text-center">
                    <h3>Compare</h3>
                    <p>Add a {{ indicator.currentLocation.location_type }}</p>
                </div>
                <button 
                    @click="handleComparisonActivated"
                    class="block w-fit mx-auto p-1 bg-gray-700 text-white my-3"
                    >Add A Comparison</button>
                <section v-if="comparisonIsActivated" class="border-2 border-gray-700 p-3">
                    <Select 
                        :options="locations" 
                        optionLabel="label" 
                        placeholder="Select a Location" 
                        @change="handleLocationSelected"
                        :pt="{
                            root: {
                                class: 'relative p-3 rounded-lg border-2 border-gray-700'
                            },
                            dropdownIcon: {
                                class: 'absolute right-0 inset-y-1/2 -translate-y-1/2 mr-3'
                            },
                            listContainer: {
                                class: 'p-3 overflow-y-auto bg-white border-b-2 border-x-2 border-gray-700 shadow-sm'
                            },
                            option: {
                                class: 'hover:bg-gray-700 hover:text-white focus-visible:bg-gray-700 focus-visible:text-white'
                            }
                        }"
                    >
                </Select>
                </section>
            </section>
        </section>
</template>