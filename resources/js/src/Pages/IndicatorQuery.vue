<script lang="ts" setup>
import AppLayout from '../Layouts/AppLayout.vue';
import {Head, usePage} from '@inertiajs/vue3';
import { useIndicatorsStore } from '../../stores/indicators';
import type { Indicator, IndicatorData, IndicatorFilters, SelectedFilters} from '../../types/indicators';
import QueryTable from '../Partials/IndicatorQuery/QueryTable.vue';

defineOptions({
    layout: AppLayout
})

const page = usePage<{
    indicator: Indicator,
    data: IndicatorData[],
    data_count: number,
    initial_filters: SelectedFilters,
    filters: IndicatorFilters
}>();

const indicator = useIndicatorsStore();

indicator.indicator = page.props.indicator;
indicator.indicatorData = page.props.data;
indicator.indicatorDataCount = page.props.data_count;
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
        <h1>Query {{ indicator.indicator.name }}</h1>
        <QueryTable/>
    </section>
</template>