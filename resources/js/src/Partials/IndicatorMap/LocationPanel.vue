<script lang="ts" setup>
import { useIndicatorsStore } from '../../../stores/indicators';
import { Select } from 'primevue';

const indicator = useIndicatorsStore();

</script>

<template>
        <section
            class="absolute top-0 right-0 w-[25vw] border-l-2 border-gray-700"
            >
            <h2 class="text-center">Location</h2>
            <ul
                class="flex h-48 gap-x-3"
                >
                <li
                    v-for="location in indicator.locationIndicatorData">
                    <div class="bg-gray-700 text-white">{{ location.timeframe }}: {{ location.data }}</div>
                </li>
            </ul>
        </section>
</template>