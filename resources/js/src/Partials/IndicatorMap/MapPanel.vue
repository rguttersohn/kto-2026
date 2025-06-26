<script lang="ts" setup>
import { useIndicatorsStore } from '../../../stores/indicators';
import { useErrorStore } from '../../../stores/errors';
import { fetchLocationIndicatorData } from '../../../services/fetch/fetch-locations';

const indicator = useIndicatorsStore();
const errors = useErrorStore();

async function handleLocationClicked(event:Event){

    if(!indicator.indicator){

        return;
        
    }

    const target = event.target as HTMLPreElement;

    const locationID = target.dataset.locationId;

    if(!locationID){
        console.error('missing location id');
        return;
    }

    const reducedSelectedFilters = indicator.getReducedSelectedFilters('timeframe');

    const params = indicator.getFiltersAsParams(reducedSelectedFilters);

    const {error, data} = await fetchLocationIndicatorData(parseInt(locationID), indicator.indicator.id, params);

    if(error.status){
        
        errors.error = true;

        errors.errorMessage = error.message;
    }

    indicator.currentLocation = data[0];
    indicator.locationIndicatorData = data;

}

</script>


<template>
    <section class="ml-[30vw]">
        <h2>Data</h2>
        <ul 
            v-if="indicator.indicatorData"
            @click="handleLocationClicked"
            >
            <li
                v-for="(data, index) in indicator.indicatorData?.features"
                :key="index"
                >
                <pre
                    :data-location-Id="data.properties.location_id"
                >
                    {{  data.properties }}
                </pre>
                </li>
        </ul>
    </section>
</template>