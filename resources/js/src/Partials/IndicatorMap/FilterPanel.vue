
<script lang="ts" setup>
import { computed } from 'vue';
import type { FilterSelectOption, FilterGroupSelectOption } from '../../../types/indicators';
import { SelectChangeEvent, Select } from 'primevue';
import { useIndicatorsStore } from '../../../stores/indicators';
import { fetchIndicatorGeoJSONData } from '../../../services/fetch/fetch-indicators';
import { useErrorStore } from '../../../stores/errors';
import { useSyncFiltersToURL } from '../../../composables/sync-filter-params';

const indicator = useIndicatorsStore();
const errorsStore = useErrorStore();
useSyncFiltersToURL();

async function handleFilterSelected(event:SelectChangeEvent){

    if(!indicator.indicator){

        return;
    }

    if(!indicator.selectedFilters){
        
        return;

    }

    indicator.updateSelectedFilters(event.value);

    indicator.getFiltersAsParams(indicator.selectedFilters);

   const {data, error} = await fetchIndicatorGeoJSONData(indicator.indicator.id, indicator.getFiltersAsParams(indicator.selectedFilters));

   if(error.status){

        errorsStore.error = error.status;
        
        errorsStore.errorMessage = error.message;

        return;

   }

    if(!data){

        return;
    }

    indicator.indicatorData = data;
    indicator.emptyCurrentLocation();

}

const timeframeOptions = computed(():Array<FilterSelectOption>=>{

    
    if(!indicator.indicatorFilters){

        return [];
    }
    
    return indicator.indicatorFilters.timeframe.map(t=>({
        name: 'timeframe',
        value: t,
        label: t
    }))

});

const currentTimeFrameLabel = computed((): string | number =>{
    
    if(!indicator.selectedFilters){

        return 'Filter by Year';
    }

    const currentTimeframeFilter = indicator.selectedFilters.find(filter=>filter.name === 'timeframe');

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
    
    if(!indicator.indicatorFilters){

        return [];
    }

    return indicator.indicatorFilters.location_type.map(location=>({
        name:'location_type',
        value: location.id,
        label: location.plural_name
    }))
})

const currentLocationTypeLabel = computed(():string | number =>{

    if(!indicator.selectedFilters){

        return 'Filter by Location Type';
    }

    const currentLocationTypeFilter = indicator.selectedFilters.find(filter=>filter.name === 'location_type');
    
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

    if(!indicator.indicatorFilters){

        return [];

    }

    return indicator.indicatorFilters.format.map(f=>({
        name: 'format',
        value: f.id,
        label: f.name
    }))

})

const currentformatLabel = computed(():string | number =>{

    if(!indicator.selectedFilters){

        return 'Filter by Format';
    }

    const currentformatFilter = indicator.selectedFilters.find(filter=>filter.name === 'format');

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

    if(!indicator.indicatorFilters){
        
        return [];

    }

return indicator.indicatorFilters.breakdown.map(b=>({
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

    if(!indicator.selectedFilters){

        return 'Filter by Breakdown';

    }

    const currentBreakdownFilter = indicator.selectedFilters.find(filter=>filter.name === 'breakdown');
  
    if(!currentBreakdownFilter){

        return 'Filter by Breakdown';
    }

    const breakdownItems = breakdownOptions.value.flatMap(group=>group.items);

    const currentBreakdownOption = breakdownItems.find(option=>option.value === currentBreakdownFilter.value);

    if(!currentBreakdownOption){

        return 'Filter by Breakdown';

    } 

    return currentBreakdownOption.label;

})

</script>

<template>
    <section class="absolute left-0 flex-col w-[25vw] p-10 border-2 rounded-lg bg-white shadow-sm">
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
                <template #value>
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
                <template #value>
                    {{ currentBreakdownLabel }}
                </template>
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