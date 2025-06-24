<script lang="ts" setup>
import AppLayout from '../Layouts/AppLayout.vue';
import { Link } from '@inertiajs/vue3';


defineOptions({
    layout:AppLayout
});


const props = defineProps<{
    indicators: Array<{
        id: number,
        name: string,
        slug: string
    }>
}>()

</script>

<template>
    <h1>Indicators</h1>

    <ul>
        <li 
            v-for="indicator in props.indicators"
            :key="indicator.id"
            >
            <Link :href="`indicators/${indicator.id}`">{{ indicator.name }}</Link>
        </li>
    </ul>
</template>