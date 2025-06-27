<script lang="ts" setup>
import AppLayout from '../Layouts/AppLayout.vue';
import { Link, Head } from '@inertiajs/vue3';

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
    }
}>()
</script>

<template>
    <Head>
        <title>{{ props.indicator?.name ?? 'Indicator' }}</title>
    </Head>
    <h1>{{ props.indicator.name }}</h1>
    <p>{{ props.indicator.definition }}</p>
    <Link 
        :href="`/indicators/${props.indicator.id}/map`"
        class="p-3 border-2"
        >Map Indicator</Link>
    <Link :href="`/indicators/${props.indicator.id}/query`">Query Indicator</Link>

    
</template>