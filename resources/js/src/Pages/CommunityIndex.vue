<script lang="ts" setup>
import AppLayout from '../Layouts/AppLayout.vue';
import { Location } from '../../types/locations';
import IndicatorSection from '../Partials/CommunityIndex/IndicatorSection.vue'

    defineOptions({
        layout: AppLayout
    })

    const props = defineProps<{
        location: Location,
    }>();
     
</script>

<template>
    <h1>{{ props.location.name}} Community Profile</h1>
    <IndicatorSection />
</template>