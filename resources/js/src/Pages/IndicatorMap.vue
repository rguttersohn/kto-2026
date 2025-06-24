<script lang="ts" setup>
import AppLayout from '../Layouts/AppLayout.vue';
import Select from 'primevue/select';
import {computed, ref, watch} from 'vue';

    defineOptions({
        layout: AppLayout
    })

    const props = defineProps<{
        indicator: {
            id: number, 
            name: string,
            slug: string,
            definition: string,
            source: string,
            note: string,
            data: any
        },
        filters: {
            timeframe: any,
            location_type: any,
            format: any,
            breakdown: any
        }
    }>();

const timeframes = computed(()=>props.filters.timeframe.map(timeframe=>({ value: timeframe, name: timeframe})));

const selectedTimeframe = ref<any>({ value: 2024, code:  2024 })

const locationTypes = computed(()=>props.filters.location_type.map(location_type=>({ value: location_type.id, name: location_type.plural_name})))

const selectedLocationType = ref<any>(null);

const formats = computed(()=>props.filters.format.map(format=>({ value: format.id, name: format.name})))

const selectedFormat = ref<any>(null);


watch([selectedTimeframe, selectedLocationType, selectedFormat],async()=>{

    const url = `/api/app/indicators/${props.indicator.id}/data?${ selectedTimeframe.value ? 'filter[timeframe][eq]=' + selectedTimeframe.value : ''}`

    console.log(url)
})


</script>

<template>
    <h1>Map {{ props.indicator.name }}</h1>
    <section class="flex-col w-[33vw] p-3 border-2 rounded-lg shadow-sm">
        <h2>Filters</h2>
        <div class="flex justify-center">
            <Select 
                :options="timeframes" 
                optionLabel="name" 
                placeholder="Select a City" 
                @change="selectedTimeframe = event.value"
                class="w-full md:w-56" />
        </div>
        <div class="my-10 w-1/4">
            <label for="select-breakdown">Select a Breakdown</label>
            <div
                v-for="breakdown in filters.breakdown"
                :key="breakdown.id"
                >
                <label
                    :for="`select-${breakdown.name}`">
                    {{ breakdown.name }}
                </label>
                <select
                    :id="`select-${breakdown.name}`">
                    <option
                        v-for="sub in breakdown.sub_breakdowns"
                        :key="sub.id"
                        :value="sub.id"
                    >{{ sub.name }}</option>
                </select>
            </div>
        </div>
        <div class="flex justify-center">
            <Select 
                :options="locationTypes"
                optionLabel="name"
                placeholder="Select a Location Type"
                @change="selectedLocationType = event.value"
                class="w-full md:w-56"
            />
        </div>
        <div class="flex flex-col">
            <Select 
                :options="formats"
                optionLabel="name"
                placeholder="Select Data Format"
                @change="selectedFormat = event.value"
                class="w-full md:w-56"
            />
        </div>
    </section>
</template>