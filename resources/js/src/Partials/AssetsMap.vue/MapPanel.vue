<script lang="ts" setup>
import { useAssetsStore } from '../../../stores/assets';

const asset = useAssetsStore();

</script>

<template>
    <ul v-if="asset.assetsGeoJSON">
        <li
            v-for="asset in asset.assetsGeoJSON.features"
            :key="asset.properties.id"
            >
            <pre>{{asset.properties}}</pre>
        </li>
    </ul>
</template>