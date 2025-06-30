<script lang="ts" setup>
import { SelectChangeEvent, Select,InputNumber, InputNumberInputEvent} from "primevue";
import { computed, ref, toRaw, isReactive } from 'vue';
import { FilterOperators, SelectedFilter } from "../../../types/indicators";
import { useIndicatorsStore } from "../../../stores/indicators";

const props = defineProps<{
    filter: SelectedFilter
}>();

const emit = defineEmits(['queryUpdated', 'addQuery', 'removeQuery']);

const indicator = useIndicatorsStore();

const filterNameOptions = ref<{
    label: string,
    value: string
}[]>([
    {
        label: 'Data',
        value: 'data'
    },
    {
        label: 'Year',
        value: 'timeframe'
    },
    {
        label: 'Breakdown',
        value: 'breakdown'
    },
    {
        label: 'Format',
        value: 'format'
    },
    {
        label: 'Location Type',
        value: 'location_type'
    }
]);

const operatorOptions = ref<{
    label: string,
    value: FilterOperators
}[]>([
  { label: 'Equals', value: 'eq' },
  { label: 'Not Equal To', value: 'neq' },
  { label: 'Greater Than', value: 'gt' },
  { label: 'Greater Than or Equal To', value: 'gte' },
  { label: 'Less Than', value: 'lt' },
  { label: 'Less Than or Equal To', value: 'lte' },
  { label: 'In List', value: 'in' },
  { label: 'Not In List', value: 'nin' },
  { label: 'Is Null', value: 'null' },
  { label: 'Is Not Null', value: 'notnull' },
]);


function getCloneQueryContainer(condition: SelectedFilter): SelectedFilter {
  
  const raw = isReactive(condition) ? toRaw(condition) : condition;

  return {
      id: raw.id,
      filterName: { ...toRaw(raw.filterName) },
      operator: { ...toRaw(raw.operator) },
      value: { ...toRaw(raw.value) },
  };

}


function handleFilterNameSelected(event: SelectChangeEvent) {

    const clone = getCloneQueryContainer(props.filter);

    clone.filterName = event.value;

    emit('queryUpdated', clone);

}


function handleOperatorSelected(event: SelectChangeEvent) {

    const clone = getCloneQueryContainer(props.filter);

    clone.operator = event.value;

    emit('queryUpdated', clone);

}

function handleFilterValueSelected(event: SelectChangeEvent) {
    
    const clone = getCloneQueryContainer(props.filter);

    clone.value = event.value;

    emit('queryUpdated', clone);
    
}

function handleNumberInput(event: InputNumberInputEvent){
    
    if(!event.value){
        return;
    }

    const clone = getCloneQueryContainer(props.filter);

    clone.value.label = event.formattedValue;

    clone.value.value = event.value;

    emit('queryUpdated', clone);
}


const currentFilterOptions = computed(() => {
    
    if (!props.filter.filterName.value) {
        return [];
    }

    if (props.filter.filterName.value === "timeframe") {
        return indicator.timeframeOptions;
    }

    if (props.filter.filterName.value === "location_type") {
        return indicator.locationTypeOptions;
    }

    if (props.filter.filterName.value === "format") {
        return indicator.formatOptions;
    }

    if (props.filter.filterName.value === "breakdown") {
        return indicator.breakdownOptions;
    }

    return [];
});

const containerIsReady = computed(():boolean=>{

    return !!(
      props.filter.filterName?.value &&
      props.filter.operator?.value &&
      props.filter.value?.value
    );
    
})

const isLastQuery = computed(() => {
  
    const lastQuery = indicator.selectedFilters.at(-1);
    return lastQuery?.id === props.filter.id;

});

</script>

<template>
    <div class="flex gap-x-10">
        <Select
            :options="filterNameOptions"
            optionLabel="label"
            @change="handleFilterNameSelected"
            :pt="{
                root: {
                    class: 'w-96 relative p-3 rounded-lg border-2 border-gray-700',
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
                {{ props.filter.filterName.label ?? 'Select a Filter' }}
            </template>
        </Select>
        <Select
            :options="operatorOptions"
            optionLabel="label"
            @change="handleOperatorSelected"
            :pt="{
                root: {
                    class: 'w-96 relative p-3 rounded-lg border-2 border-gray-700',
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
                {{
                    props.filter.operator.label ??
                    "Select an Operator"
                }}
            </template>
        </Select>
        
        <InputNumber 
            v-if="props.filter.filterName.value === 'data'"
            :min="0"
            :min-fraction-digits="2"
            @input="handleNumberInput"
            :pt="{
                root: {
                    class: 'w-96 relative p-3 rounded-lg border-2 border-gray-700',
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
        />
        <Select 
            v-else-if="props.filter.filterName.value === 'breakdown'"
            :options="currentFilterOptions"
            optionLabel="label"
            optionGroupLabel="groupLabel"
            optionGroupChildren="items"
            @change="handleFilterValueSelected"
            :pt="{
                root: {
                    class: 'w-96 relative p-3 rounded-lg border-2 border-gray-700',
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
        ></Select>
        <Select
            v-else
            :options="currentFilterOptions"
            optionLabel="label"
            @change="handleFilterValueSelected"
            :pt="{
                root: {
                    class: 'w-96 relative p-3 rounded-lg border-2 border-gray-700',
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
                {{ "Select a Value" }}
            </template>
        </Select>
        <div class="flex gap-x-3">
            <button
                v-if="isLastQuery"
                :disabled="!containerIsReady"
                @click="emit('addQuery')"
                class="w-36 p-3 bg-gray-700 text-white disabled:opacity-50"
                >
                Add Filter
            </button>
            <button
                @click="emit('removeQuery', props.filter.id)"
                class="w-36 p-3 bg-gray-700 text-white disabled:opacity-50"
                >
                Remove Filter
            </button>
        </div>
    </div>
</template>
