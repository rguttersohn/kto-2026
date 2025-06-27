<script lang="ts" setup>
import { usePage, Head } from '@inertiajs/vue3';
import AppLayout from '../Layouts/AppLayout.vue';
import { onBeforeUnmount, onMounted } from 'vue';
import { Indicator, IndicatorFilters, SelectedFilters, IndicatorFeature} from '../../types/indicators';
import { useIndicatorsStore } from '../../stores/indicators';
import FilterPanel from '../Partials/IndicatorMap/FilterPanel.vue';
import LocationPanel from '../Partials/IndicatorMap/LocationPanel.vue';
import MapPanel from '../Partials/IndicatorMap/MapPanel.vue';
import { useSyncCurrentLocationParam } from '../../composables/sync-current-location-param';
import { useSearchParams } from '../../composables/search-params';
import { fetchLocationIndicatorData } from '../../services/fetch/fetch-locations';
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
const params = useSearchParams();
const errors = useErrorStore();

useSyncCurrentLocationParam();

indicator.indicator = page.props.indicator;
indicator.indicatorData = page.props.data;
indicator.indicatorFilters = page.props.filters;
indicator.selectedFilters = page.props.initial_filters.map(filter=>({
    ...filter,
    id: crypto.randomUUID()
}));

onMounted(async ()=>{

    if(!indicator.indicator){
        return;
    }
   
    const currentLocationParam = params.getParam('current-location');

    if(!currentLocationParam){
        return;
    }

    const locationID = parseInt(currentLocationParam);

    const reducedSelectedFilter = indicator.getReducedSelectedFilters('timeframe');

    const filterParamString = indicator.getFiltersAsParams(reducedSelectedFilter);

    const {error, data} = await fetchLocationIndicatorData(locationID, indicator.indicator.id, filterParamString);

    if(error.status){
        
        errors.error = true;

        errors.errorMessage = error.message;
    }

    indicator.currentLocation = data[0];
    indicator.locationIndicatorData = data;
})

onBeforeUnmount(()=>{
    indicator.emptyCurrentLocation();
    indicator.emptyComparedLocations();
})


</script>


<template>
    <Head>
        <title>Map {{ indicator.indicator?.name ?? 'Indicator' }}</title>
    </Head>
    <section 
        v-if="indicator.indicator" 
        class="py-1 px-10 border-b-2 border-gray-700 bg-white">
        <h1>Map {{ indicator.indicator.name }}</h1>
    </section>
    <div class="relative">
        <FilterPanel/>
        <MapPanel/>
        <LocationPanel v-if="indicator.currentLocation"/>
    </div>
</template>