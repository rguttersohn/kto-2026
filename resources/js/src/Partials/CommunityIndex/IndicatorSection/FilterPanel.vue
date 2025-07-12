<script lang="ts" setup>
import {computed} from 'vue';
import { useIndicatorsStore } from '../../../../stores/indicators';
import { FilterSelectOption } from '../../../../types/indicators';
import { Select, SelectChangeEvent } from 'primevue';
import { fetchLocationIndicatorData } from '../../../../services/fetch/fetch-locations';
import { useErrorStore } from '../../../../stores/errors';
import { Location } from '../../../../types/locations';

const indicator = useIndicatorsStore();
const error = useErrorStore();

const props = defineProps<{
    location:Location
}>()

const currentBreakdownLabel = computed((): string | number => {
    
    if (indicator.selectedFilters.length === 0) {
        return "Filter by Breakdown";
    }

    const match = indicator.selectedFilters.find(
        (filter) => filter.filterName.value === "breakdown"
    );

    if (!match) {
        return "Filter by Breakdown";
    }

    if (!match.value.label) {
        return "Filter by Breakdown";
    }

    return Array.isArray(match.value.label)
        ? match.value.label[0]
        : match.value.label;

});


const currentformatLabel = computed((): string | number => {
   
    if (indicator.selectedFilters.length === 0) {
        return "Filter by Format";
    }

    const match = indicator.selectedFilters.find(
        (filter) => filter.filterName.value === "format"
    );

    if (!match) {
        return "Filter by Format";
    }

    if (!match.value.label) {
        return "Filter by Format";
    }

    return Array.isArray(match.value.label)
        ? match.value.label[0]
        : match.value.label;

});

function updateSelectedFilters(filterSelectOption: FilterSelectOption) {
    
    const currentFilterIndex = indicator.selectedFilters.findIndex(
        (filter) => (filter.filterName.value === filterSelectOption.name)
    );


    if (currentFilterIndex === -1) {
        return;
    }

    indicator.selectedFilters[currentFilterIndex].value = {
        label: filterSelectOption.label.toString(),
        value: filterSelectOption.value,
    };

}

async function handleFilterSelected(event: SelectChangeEvent){

    if (!indicator.indicator) {
        return;
    }

    if (!indicator.selectedFilters) {
        return;
    }

    updateSelectedFilters(event.value);

    const locationID = props.location.id;
    const indicatorID = indicator.indicator.id;

    const params = indicator.getFiltersAsParams(indicator.selectedFilters);

    const {data, error:responseError} = await fetchLocationIndicatorData(locationID, indicatorID, params);

    if(responseError.status){

        error.error = true;
        error.errorMessage = responseError.message
    }

    indicator.indicatorData = data;
}

</script>

<template>
    <section class="basis-1/4 p-3">
        <h3>filters</h3>
        <div class="my-10">
            <Select
                :options="indicator.breakdownOptions"
                optionLabel="label"
                optionGroupLabel="groupLabel"
                optionGroupChildren="items"
                placeholder="Breakdown"
                @change="handleFilterSelected"
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
                <template #value>
                    {{ currentBreakdownLabel }}
                </template>
                <template #optiongroup="slotProps">
                    <span class="font-bold">{{
                        slotProps.option.groupLabel
                    }}</span>
                </template>
                <template #option="slotProps">
                    <span class="ml-3">{{ slotProps.option.label }}</span>
                </template>
            </Select>
        </div>
        <div class="my-10">
            <Select
                :options="indicator.formatOptions"
                optionLabel="label"
                placeholder="Data Format"
                @change="handleFilterSelected"
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
                <template #value>
                    {{ currentformatLabel }}
                </template>
            </Select>
        </div>
    </section>
</template>