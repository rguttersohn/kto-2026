<script lang="ts" setup>
import AppLayout from '../Layouts/AppLayout.vue';
import {Head} from '@inertiajs/vue3';

    defineOptions({
        layout: AppLayout
    })

    const props = defineProps<{
        indicator: {
            id: number, 
            name: string,
            slug: string,
            definition: string,
            source: string,
            note: string,
            data: any
        }
    }>();

</script>

<template>
    <Head>
        <title>Query {{ props.indicator?.name ?? 'Indicator' }}</title>
    </Head>
    <h1>Query {{ props.indicator.name }}</h1>
</template>