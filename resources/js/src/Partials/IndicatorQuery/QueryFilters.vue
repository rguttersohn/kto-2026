<script lang="ts" setup>
import { useIndicatorsStore } from '../../../stores/indicators';
import { fetchIndicatorData } from '../../../services/fetch/fetch-indicators';
import { useErrorStore } from '../../../stores/errors';
import { onMounted, computed } from 'vue';
import CreateFilter from './CreateFilter.vue';
import { SelectedFilter } from '../../../types/indicators';

const indicator = useIndicatorsStore();
const errors = useErrorStore();


onMounted(()=>{

    if(indicator.selectedFilters.length === 0){

        indicator.selectedFilters.push(indicator.generateFilterContainer());

    }
})

async function handleQuerySubmitted(){

    if(!indicator.indicator){
        
        console.error('missing indicator');

        return;
    }

    const params = indicator.getFiltersAsParams(indicator.selectedFilters);

    const indicatorID = indicator.indicator.id;

    const {data, error} = await fetchIndicatorData(indicatorID, params, 50, indicator.queryOffset);

    if(error.status){

        errors.error = true;

        errors.errorMessage = error.message;
        
    }

    indicator.indicatorData = data;

}

function handleQueryUpdated(filter: SelectedFilter){

    let updatedQueryIndex = indicator.selectedFilters.findIndex(selectedFilter=>selectedFilter.id === filter.id)

    if(updatedQueryIndex === -1){

        return;

    }

    indicator.selectedFilters[updatedQueryIndex] = filter;

}

function handleQueryAdded(){

    indicator.selectedFilters.push(indicator.generateFilterContainer());

}

function handleQueryRemoved(filterID:string){


    let updatedQueryIndex = indicator.selectedFilters.findIndex(selectedFilter=>selectedFilter.id === filterID)

    if(updatedQueryIndex === -1){

        return;

    }

    indicator.selectedFilters.splice(updatedQueryIndex, 1);

}


const allQueriesAreReady = computed(() => {
  return indicator.selectedFilters.every(query => {
    return !!(
      query.filterName?.value &&
      query.operator?.value &&
      query.value?.value
    );
  });
});


</script>

<template>
    <section class="mx-auto w-10/12 my-10">        
        <ul>
            <li 
                v-for="filter in indicator.selectedFilters"
                :key="filter.id"
                >
                <CreateFilter
                    :filter="filter"
                    @queryUpdated="handleQueryUpdated"
                    @addQuery="handleQueryAdded"
                    @removeQuery="handleQueryRemoved"
                />
            </li>
        </ul>
            
        <div class="my-10">
            <button
                @click="handleQuerySubmitted"
                :disabled="!allQueriesAreReady"
                class="p-3 bg-gray-700 text-white disabled:opacity-50"
                >
                Submit Query
            </button>
        </div>
    </section>
</template>