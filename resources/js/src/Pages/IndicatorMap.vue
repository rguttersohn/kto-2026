<script lang="ts" setup>
import { usePage } from '@inertiajs/vue3';
import AppLayout from '../Layouts/AppLayout.vue';
import {onBeforeMount } from 'vue';
import { Indicator, IndicatorFilters, SelectedFilters, IndicatorFeature} from '../../types/indicators';
import { useIndicatorsStore } from '../../stores/indicators';
import FilterPanel from '../Partials/IndicatorMap/FilterPanel.vue';

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
    <section v-if="indicator.indicator" class="my-10 bg-white">
        <h1>Map {{ indicator.indicator.name }}</h1>
    </section>
    <FilterPanel/>
    <section>
        <h2>Data</h2>
        <ul v-if="indicator.indicatorData">
            <li 
                v-for="(data, index) in indicator.indicatorData?.features"
                :key="index"
                >
                <pre>{{  data.properties }}</pre>
                </li>
        </ul>
    </section>
</template>