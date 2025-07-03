<script lang="ts" setup>
import { Head } from "@inertiajs/vue3";
import { ParentCategory } from "../../types/assets";
import { LocationType } from "../../types/locations";
import AppLayout from "../Layouts/AppLayout.vue";
import SelectAssets from '../Partials/AssetsMap.vue/SelectAssets.vue'

defineOptions({
  layout: AppLayout,
});

const props = defineProps<{
  asset_categories: ParentCategory[];
  location_types: LocationType[];
}>();



</script>

<template>
  <Head>
    <title>Community Resources</title>
  </Head>
  <section class="w-screen">
    <h1>Community Resources</h1>
    <section class="w-2/4 ml-20 my-10 border-2 rounded-lg border-gray-700">
      <SelectAssets :asset_categories />
    </section>
  </section>
</template>
