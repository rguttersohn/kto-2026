<script lang="ts" setup>
import AppLayout from '../Layouts/AppLayout.vue';

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
    <h1>Map {{ props.indicator.name }}</h1>
</template>