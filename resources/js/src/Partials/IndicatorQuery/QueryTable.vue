<script lang="ts" setup>
import { DataTable, Column, DataTablePageEvent } from 'primevue';
import { useIndicatorsStore } from '../../../stores/indicators';
import { fetchIndicatorData} from '../../../services/fetch/fetch-indicators';
import { useErrorStore } from '../../../stores/errors';

const indicator = useIndicatorsStore();
const errors = useErrorStore();

async function handlePaginate(event:DataTablePageEvent){

    if(!indicator.indicator){
        
        console.error('missing indicator');

        return;
    }
    
    const offset = event.page;

    const params = indicator.getFiltersAsParams(indicator.selectedFilters);

    const indicatorID = indicator.indicator.id;

    const {data, error} = await fetchIndicatorData(indicatorID, params, 50, offset);

    if(error.status){

        errors.error = true;

        errors.errorMessage = error.message;
        
    }

    indicator.indicatorData = data;

}

</script>

<template>
    <section 
        class="w-10/12 mx-auto"
        >
        <h2>data table</h2>
        <DataTable 
            :lazy="true"
            :value="indicator.indicatorData"
            removableSort
            scrollable
            scrollHeight="600px"
            paginator
            :totalRecords="indicator.indicatorDataCount"
            :rows="50"
            @page="handlePaginate"
            :pt="{
                table:{
                    class: 'w-10/12 mx-auto'
                },
                row: {
                    class: 'bg-blue-400'
                },
                column: {
                    headerCell: {
                        class:'w-24 overflow-x-hidden bg-gray-100'
                    },
                    columnHeaderContent: {
                        class: 'px-3 py-1 text-gray-700 text-center'
                    }
                }
                }"
            >
            <Column field="data" header="Data"></Column>
            <Column field="timeframe" header="Year"></Column>
            <Column field="breakdown" header="Breakdown"></Column>
            <Column field="location_type" header="Location Type"></Column>
            <Column field="location" header="Location"></Column>
            <Column field="format" header="Format"></Column>
        </DataTable>
    </section>
</template>