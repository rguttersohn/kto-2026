<script lang="ts" setup>
import { usePage } from '@inertiajs/vue3';
import AppLayout from '../Layouts/AppLayout.vue';
import {onBeforeMount } from 'vue';
import { Indicator, IndicatorFilters, SelectedFilters, IndicatorFeature} from '../../types/indicators';
import { useIndicatorsStore } from '../../stores/indicators';
import FilterPanel from '../Partials/IndicatorMap/FilterPanel.vue';
import LocationPanel from '../Partials/IndicatorMap/LocationPanel.vue';
import MapPanel from '../Partials/IndicatorMap/MapPanel.vue';

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

onBeforeMount(()=>{

    indicator.indicator = page.props.indicator;
    indicator.indicatorData = page.props.data;
    indicator.selectedFilters = page.props.initial_filters;
    indicator.indicatorFilters = page.props.filters;

})



</script>

<template>
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