<script lang="ts" setup>
import { useIndicatorsStore } from '../../../stores/indicators';
import { fetchIndicatorData } from '../../../services/fetch/fetch-indicators';
import { useErrorStore } from '../../../stores/errors';
import { onMounted, computed } from 'vue';
import CreateFilter from './CreateFilter.vue';
import { QueryBuilderContainer } from '../../../types/indicators';

const indicator = useIndicatorsStore();
const errors = useErrorStore();


onMounted(()=>{

    if(indicator.queryContainer.length === 0){

        indicator.queryContainer.push(indicator.generateQueryContainer());

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

function handleQueryUpdated(query:QueryBuilderContainer){


    let updatedQueryIndex = indicator.queryContainer.findIndex(query=>query.id === query.id)

    if(updatedQueryIndex === -1){

        return;

    }

    indicator.queryContainer[updatedQueryIndex] = query;

}


const allQueriesAreReady = computed(() => {
  return indicator.queryContainer.every(query => {
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
                v-for="query in indicator.queryContainer"
                :key="query.id"
                >
                <CreateFilter
                    :query="query"
                    @queryUpdated="handleQueryUpdated"
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