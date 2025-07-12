<script lang="ts" setup>
import {computed, watch} from 'vue';
import { usePage } from '@inertiajs/vue3';
import SelectAssets from './AssetsSection/SelectAssets.vue';
import { ParentCategory } from '../../../types/assets';
import { useAssetsStore } from '../../../stores/assets';

const page = usePage<{
    asset_categories: ParentCategory[],
    location: Location
}>();

const assets = useAssetsStore();

assets.assetCategories = page.props.asset_categories;

const selectedCategoryIDs = computed(()=>assets.selectedCategoryIDs);

watch(selectedCategoryIDs, ()=>{

    
});

</script>

<template>
    <section class="my-20 px-20">
        <h2>Community Assets and Resource</h2>
        <div class="flex">
            <section class="basis-1/4">
               <SelectAssets :location="page.props.location" />
            </section>
            <section class="basis-3/4">
                <ul>
                    <li v-for="asset in assets.assetsGeoJSON?.features" :key="asset.id">
                        <pre>{{ asset.properties }}</pre>
                    </li>
                </ul>
            </section>
        </div>
    </section>
</template>