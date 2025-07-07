<script lang="ts" setup>
import { Head, usePage } from "@inertiajs/vue3";
import { ParentCategory } from "../../types/assets";
import AppLayout from "../Layouts/AppLayout.vue";
import SelectAssets from '../Partials/AssetsMap.vue/SelectAssets.vue'
import MapPanel from '../Partials/AssetsMap.vue/MapPanel.vue';
import { useAssetsStore } from "../../stores/assets";
import { LocationType } from "../../types/locations";
import SelectionLocation from '../Partials/AssetsMap.vue/SelectionLocation.vue'
import CustomLocation from '../Partials/AssetsMap.vue/CustomLocation.vue'

defineOptions({
  layout: AppLayout,
});

const page = usePage<{
  asset_categories: ParentCategory[],
  location_types: LocationType[]
}>();

const asset = useAssetsStore();

asset.assetCategories = page.props.asset_categories;
asset.assetLocationTypes = page.props.location_types;

</script>

<template>
  <Head>
    <title>Community Resources</title>
  </Head>
  <section class="relative w-screen">
    <h1>Community Resources</h1>
    <section class="absolute w-96 h-96 flex flex-col justify-evenly ml-3 my-10 border-2 rounded-lg border-gray-700 bg-white">
      <SelectAssets />
      <SelectionLocation />
      <CustomLocation />
    </section>
    <MapPanel />
  </section>
</template>
