<script lang="ts" setup>
import {computed, watch} from 'vue';
import { usePage } from '@inertiajs/vue3';
import SelectAssets from './AssetsSection/SelectAssets.vue';
import { ParentCategory } from '../../../types/assets';
import { useAssetsStore } from '../../../stores/assets';
import { fetchAssetsAsGeoJSONByLocation } from '../../../services/fetch/fetch-assets';
import { Location } from '../../../types/locations';
import { useErrorStore } from '../../../stores/errors';

const page = usePage<{
    asset_categories: ParentCategory[],
    location: Location
}>();

const assets = useAssetsStore();
const error = useErrorStore();

assets.assetCategories = page.props.asset_categories;

const selectedCategoryIDs = computed(()=>assets.selectedCategoryIDs);

watch(selectedCategoryIDs, async ()=>{

    const locationID = page.props.location.id;

    const params = assets.getIDsAsParams(assets.selectedCategoryIDs);

    const {data, error: responseError} = await fetchAssetsAsGeoJSONByLocation(locationID,params);

    if(responseError.status){

        error.error = true;
        error.errorMessage = responseError.message;
        
        return;
    }

    assets.assetsGeoJSON = data;
    
});

</script>

<template>
    <section class="my-20 w-11/12 mx-auto">
        <h2>Community Assets and Resource</h2>
        <div class="flex border-2 border-gray-700 p-20">
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