<script lang="ts" setup>
import AppLayout from '../Layouts/AppLayout.vue';
import Select, { SelectChangeEvent } from 'primevue/select';
import {computed, ref } from 'vue';
import { Indicator, IndicatorFilters, SelectedFilters, FilterSelectOption, FilterCondition, FilterGroupSelectOption} from '../../types/indicators';
import { fetchIndicatorData } from '../../services/fetch/fetchIndicators';

    defineOptions({
        layout: AppLayout
    })

    const props = defineProps<{
        indicator: Indicator,
        filters: IndicatorFilters
    }>();

const selectedFilters = ref<SelectedFilters>([]);

function updateSelectedFilters(filterSelectOption: FilterSelectOption){

   const matchingFilterIndex = selectedFilters.value.findIndex(filterCondition=>filterCondition.name === filterSelectOption.name);

   if(matchingFilterIndex !== -1){
    
        selectedFilters.value.splice(matchingFilterIndex, 1);

   }

   selectedFilters.value.push({
        name: filterSelectOption.name,
        operator: 'eq',
        value: filterSelectOption.value
   })

}

function getFiltersAsParams(selectedFilters: SelectedFilters):string | null{

    if(selectedFilters.length === 0){
        return null;
    }


    return selectedFilters.map(filter => {
        return `filter[${filter.name}][${filter.operator}]=${Array.isArray(filter.value) ?
            filter.value.join(',') : filter.value}`;
    }).join('&');

}

const timeframeOptions = computed(():Array<FilterSelectOption>=>{
    return props.filters.timeframe.map(t=>({
        name: 'timeframe',
        value: t,
        label: t
    }))
});

function handleFilterSelected(event:SelectChangeEvent){
 
    updateSelectedFilters(event.value);

    getFiltersAsParams(selectedFilters.value);

    fetchIndicatorData(props.indicator.id, getFiltersAsParams(selectedFilters.value), true);
}

const locationTypeOptions = computed(():Array<FilterSelectOption>=>{
    return props.filters.location_type.map(location=>({
        name:'location_type',
        value: location.id,
        label: location.plural_name
    }))
})


const formatOptions = computed(():Array<FilterSelectOption>=>{

    return props.filters.format.map(f=>({
        name: 'format',
        value: f.id,
        label: f.name
    }))
})

const breakdownOptions = computed(():Array<FilterGroupSelectOption>=>{
    
    return props.filters.breakdown.map(b=>({
        groupLabel: b.name,
        value: b.id,
        items: b.sub_breakdowns.map(sub=>({
            name: 'breakdown',
            label: sub.name,
            value: sub.id
        }))
    }))

})


</script>

<template>
    <section class="my-10 bg-white">
        <h1>Map {{ props.indicator.name }}</h1>
    </section>
    <section class="flex-col w-[25vw] p-10 border-2 rounded-lg shadow-sm">
        <h2>Filters</h2>
        <div class="my-10">
            <Select 
                :options="timeframeOptions" 
                optionLabel="label" 
                placeholder="Select a City" 
                @change="handleFilterSelected"
                class="" 
            />
        </div>
        <div class="my-10">
            <Select 
                :options="locationTypeOptions"
                optionLabel="label"
                placeholder="Select a Location Type"
                @change="handleFilterSelected"
                class=""
            />
        </div>
        <div class="my-10">
            <Select 
                :options="breakdownOptions" 
                optionLabel="label" 
                optionGroupLabel="groupLabel" 
                optionGroupChildren="items" 
                placeholder="Select a Breakdown" 
                class=""
                @change="handleFilterSelected"
            >
                <template #optiongroup="slotProps">
                    <span class="font-bold">{{ slotProps.option.groupLabel }}</span>
                </template>
                <template #option="slotProps">
                    <span class="ml-3">{{ slotProps.option.label }}</span>
                </template>
            </Select>
        </div>
        <div class="my-10">
            <Select 
                :options="formatOptions"
                optionLabel="label"
                placeholder="Select Data Format"
                @change="handleFilterSelected"
                class=""
            />
        </div>
    </section>
</template>