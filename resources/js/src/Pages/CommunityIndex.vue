<script lang="ts" setup>
import AppLayout from '../Layouts/AppLayout.vue';

     defineOptions({
        layout: AppLayout
     })

     const props = defineProps<{
        location: {
            id: number, 
            name: string,
            fips: number,
            geopolitical_id: number,
        }

     }>()

     
</script>

<template>
    <h1>{{ props.location.name}} Community Profile</h1>
</template>