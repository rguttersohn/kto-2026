<script lang="ts" setup>
import { computed } from "vue";
import { SelectChangeEvent, Select } from "primevue";
import { useIndicatorsStore } from "../../../stores/indicators";
import { fetchIndicatorGeoJSONData } from "../../../services/fetch/fetch-indicators";
import { useErrorStore } from "../../../stores/errors";
import { useSyncFiltersToURL } from "../../../composables/sync-filter-params";
import { FilterSelectOption } from "../../../types/indicators";

const indicator = useIndicatorsStore();
const errorsStore = useErrorStore();
useSyncFiltersToURL();

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

async function handleFilterSelected(event: SelectChangeEvent) {
    
    if (!indicator.indicator) {
        return;
    }

    if (!indicator.selectedFilters) {
        return;
    }

    updateSelectedFilters(event.value);

    const params = indicator.getFiltersAsParams(indicator.selectedFilters);

    const { data, error } = await fetchIndicatorGeoJSONData(
        indicator.indicator.id,
        params,
        null,
        null,
        true
    );

    if (error.status) {
        errorsStore.error = error.status;

        errorsStore.errorMessage = error.message;

        return;
    }

    if (!data) {
        return;
    }

    indicator.indicatorData = data;
    indicator.emptyCurrentLocation();
}

const currentTimeFrameLabel = computed((): string | number => {
    if (indicator.selectedFilters.length === 0) {
        return "Filter by Year";
    }

    const match = indicator.selectedFilters.find(
        (filter) => filter.filterName.value === "timeframe"
    );

    if (!match) {
        return "Filter by Year";
    }

    if (!match.value.label) {
        return "Filter by Year";
    }

    return Array.isArray(match.value.label)
        ? match.value.label[0]
        : match.value.label;
});

const currentLocationTypeLabel = computed((): string | number => {
    
    if (indicator.selectedFilters.length === 0) {
        return "Filter by Location Type";
    }

    const match = indicator.selectedFilters.find(
        (filter) => filter.filterName.value === "location_type"
    );

    if (!match) {
        return "Filter by Location Type";
    }

    if (!match.value.label) {
        return "Filter by Location Type";
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
</script>

<template>
    <section
        class="absolute left-0 flex-col w-[25vw] p-10 border-2 rounded-lg bg-white shadow-sm"
    >
        <h2>Filters</h2>
        <div class="my-10">
            <Select
                :options="indicator.timeframeOptions"
                optionLabel="label"
                placeholder="Year"
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
                    {{ currentTimeFrameLabel }}
                </template>
            </Select>
        </div>
        <div class="my-10">
            <Select
                :options="indicator.locationTypeOptions"
                optionLabel="label"
                placeholder="Location Type"
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
                    {{ currentLocationTypeLabel }}
                </template>
            </Select>
        </div>
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
