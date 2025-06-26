<script lang="ts" setup>
import { usePage } from '@inertiajs/vue3';
import AppLayout from '../Layouts/AppLayout.vue';
import {onBeforeMount } from 'vue';
import { Indicator, IndicatorFilters, SelectedFilters, IndicatorFeature} from '../../types/indicators';
import { useIndicatorsStore } from '../../stores/indicators';
import FilterPanel from '../Partials/IndicatorMap/FilterPanel.vue';
import { fetchLocationIndicatorData } from '../../services/fetch/fetch-locations';
import LocationPanel from '../Partials/IndicatorMap/LocationPanel.vue';
import { useErrorStore } from '../../stores/errors';

defineOptions({
    layout: AppLayout
})

const page = usePage<{
    indicator: Indicator,
    data: IndicatorFeature,
    initial_filters: SelectedFilters,
    filters: IndicatorFilters
}>();

const indicator = useIndicatorsStore();
const errors = useErrorStore();

onBeforeMount(()=>{

    indicator.indicator = page.props.indicator;
    indicator.indicatorData = page.props.data;
    indicator.selectedFilters = page.props.initial_filters;
    indicator.indicatorFilters = page.props.filters;

})

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

    indicator.locationIndicatorData = data;
     
}

</script>

<template>
    <section 
        v-if="indicator.indicator" 
        class="my-10 bg-white">
        <h1>Map {{ indicator.indicator.name }}</h1>
    </section>
    <div class="relative">
        <FilterPanel/>
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
        <LocationPanel v-if="indicator.locationIndicatorData"/>
    </div>
</template>