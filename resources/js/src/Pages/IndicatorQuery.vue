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

    console.log(props.indicator.data);

</script>

<template>
    <h1>Query {{ props.indicator.name }}</h1>
</template>