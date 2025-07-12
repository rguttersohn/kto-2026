<script lang="ts" setup>
import AppLayout from '../Layouts/AppLayout.vue';
import { Location } from '../../types/locations';
import IndicatorSection from '../Partials/CommunityIndex/IndicatorSection.vue'
import { useSyncIndicatorParam } from '../../composables/sync-indicator-param';
import { usePage } from '@inertiajs/vue3';
import { Indicator, IndicatorData, IndicatorFilters } from '../../types/indicators';
import { useIndicatorsStore } from '../../stores/indicators';
import AssetsSection from '../Partials/CommunityIndex/AssetsSection.vue'

    defineOptions({
        layout: AppLayout
    })

    const props = defineProps<{
        location: Location,
    }>();

    useSyncIndicatorParam();
    const indicator = useIndicatorsStore();


    const page = usePage<{
        current_indicator: Indicator,
        current_indicator_filters: IndicatorFilters,
        current_indicator_data: IndicatorData[]
    }>();

    if(page.props.current_indicator){
        
        indicator.indicator = page.props.current_indicator;
    }

    if(page.props.current_indicator_filters){

        indicator.indicatorFilters = page.props.current_indicator_filters
    }

    if(page.props.current_indicator_data){
        indicator.indicatorData = page.props.current_indicator_data;
    }
    

     
</script>

<template>
    <h1>{{ props.location.name}} Community Profile</h1>
    <IndicatorSection />
    <AssetsSection />
</template>