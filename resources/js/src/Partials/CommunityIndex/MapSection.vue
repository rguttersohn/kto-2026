<script lang="ts" setup>
import { usePage } from '@inertiajs/vue3';
import { LocationFeature } from '../../../types/locations';

const page = usePage<{
    location_geojson: LocationFeature
}>();

const map = page.props.location_geojson;

console.log(map.features);
</script>

<template>
<section class="w-11/12 mx-auto my-20">
     <h2>Community Map</h2>
     <section class="w-full p-3 border-2 rounded-lg border-gray-700">
        <ul>
            <li v-for="feature in map.features"
                :key="feature.properties.id"
            >
                <pre>{{ feature.properties}}</pre>
            </li>
        </ul>
     </section>
</section>

</template>