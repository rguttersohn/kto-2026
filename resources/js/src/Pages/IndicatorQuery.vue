<script lang="ts" setup>
import AppLayout from '../Layouts/AppLayout.vue';
import {Head, usePage} from '@inertiajs/vue3';
import { useIndicatorsStore } from '../../stores/indicators';
import type { Indicator, IndicatorData, IndicatorFilters, SelectedFilter} from '../../types/indicators';
import QueryTable from '../Partials/IndicatorQuery/QueryTable.vue';
import QueryFilters from '../Partials/IndicatorQuery/QueryFilters.vue';
import { useSyncFiltersToURL } from '../../composables/sync-filter-params';
import ExportQuery from '../Partials/IndicatorQuery/ExportQuery.vue';

defineOptions({
    layout: AppLayout
})

const page = usePage<{
    indicator: Indicator,
    data: IndicatorData[],
    data_count: {count:number},
    initial_filters: SelectedFilter[],
    filters: IndicatorFilters
}>();

const indicator = useIndicatorsStore();
useSyncFiltersToURL();

indicator.indicator = page.props.indicator;
indicator.indicatorData = page.props.data;
indicator.indicatorDataCount = page.props.data_count.count;
indicator.indicatorFilters = page.props.filters;
indicator.selectedFilters = page.props.initial_filters.map(filter=>({
    ...filter,
    id: crypto.randomUUID()
}));

</script>

<template>
    <Head>
        <title>Query {{ indicator.indicator?.name ?? 'Indicator' }}</title>
    </Head>
    <section v-if="indicator.indicator">
        <div class="my-10">
            <h1>Query {{ indicator.indicator.name }}</h1>
        </div>
        <ExportQuery/>
        <QueryTable/>
        <QueryFilters/>
    </section>
</template>