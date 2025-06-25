<script lang="ts" setup>
import AppLayout from '../Layouts/AppLayout.vue';
import Select, { SelectChangeEvent } from 'primevue/select';
import {computed, onMounted, ref } from 'vue';
import { Indicator, IndicatorFilters, SelectedFilters, FilterSelectOption, FilterGroupSelectOption} from '../../types/indicators';
import { fetchIndicatorData } from '../../services/fetch/fetchIndicators';

    defineOptions({
        layout: AppLayout
    })

    const props = defineProps<{
        indicator: Indicator,
        filters: IndicatorFilters,
        initial_filters: SelectedFilters
    }>();

const selectedFilters = ref<SelectedFilters>([]);

onMounted(()=>selectedFilters.value = props.initial_filters);


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

const currentTimeFrameLabel = computed(():string | number =>{

    const currentTimeframeFilter = selectedFilters.value.find(filter=>filter.name === 'timeframe');

    if(!currentTimeframeFilter){

        return 'Filter by Year';
    }

    const currentTimeframeOption = timeframeOptions.value.find(option=>option.value === currentTimeframeFilter.value);

    if(!currentTimeframeOption){

        return 'Filter by Year';

    } 

    return currentTimeframeOption.label;
    
})

const locationTypeOptions = computed(():Array<FilterSelectOption>=>{
    return props.filters.location_type.map(location=>({
        name:'location_type',
        value: location.id,
        label: location.plural_name
    }))
})

const currentLocationTypeLabel = computed(():string | number =>{

    const currentLocationTypeFilter = selectedFilters.value.find(filter=>filter.name === 'location_type');

    if(!currentLocationTypeFilter){

        return 'Filter by Location Type';
    }

    const currentLocationTypeOption = locationTypeOptions.value.find(option=>option.value === currentLocationTypeFilter.value);

    if(!currentLocationTypeOption){

        return 'Filter by Location Type';

    } 

    return currentLocationTypeOption.label;

})


const formatOptions = computed(():Array<FilterSelectOption>=>{

    return props.filters.format.map(f=>({
        name: 'format',
        value: f.id,
        label: f.name
    }))
})

const currentformatLabel = computed(():string | number =>{

    const currentformatFilter = selectedFilters.value.find(filter=>filter.name === 'format');

    if(!currentformatFilter){

        return 'Filter by Format';
    }

    const currentformatOption = formatOptions.value.find(option=>option.value === currentformatFilter.value);

    if(!currentformatOption){

        return 'Filter by Format';

    } 

    return currentformatOption.label;

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


const currentBreakdownLabel = computed(():string | number =>{

    const currentBreakdownFilter = selectedFilters.value.find(filter=>filter.name === 'breakdown');

    if(!currentBreakdownFilter){

        return 'Filter by Breakdown';
    }

    const currentBreakdownOption = formatOptions.value.find(option=>option.value === currentBreakdownFilter.value);

    if(!currentBreakdownOption){

        return 'Filter by Breakdown';

    } 

    return currentBreakdownOption.label;

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
                placeholder="Year" 
                @change="handleFilterSelected"
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
                <template #value="slotProps">
                    {{ currentTimeFrameLabel }}
                </template>
            </Select>
        </div>
        <div class="my-10">
            <Select 
                :options="locationTypeOptions"
                optionLabel="label"
                placeholder="Location Type"
                @change="handleFilterSelected"
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
                <template #value>
                    {{ currentLocationTypeLabel }}
                </template>
            </Select>
        </div>
        <div class="my-10">
            <Select 
                :options="breakdownOptions" 
                optionLabel="label" 
                optionGroupLabel="groupLabel" 
                optionGroupChildren="items" 
                placeholder="Breakdown" 
                @change="handleFilterSelected"
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
                placeholder="Data Format"
                @change="handleFilterSelected"
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
                <template #value>
                    {{ currentformatLabel }}
                </template>
            </Select>
        </div>
    </section>
</template>