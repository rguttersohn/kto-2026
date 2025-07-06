<script lang="ts" setup>
import { useAssetsStore } from '../../../stores/assets';
import {watch, computed} from 'vue';
import { fetchAssetsAsGeoJSON, fetchAssetsAsGeoJSONByLocationType } from '../../../services/fetch/fetch-assets';
import { useErrorStore } from '../../../stores/errors';

const asset = useAssetsStore();
const error = useErrorStore();

const currentLocationTypeID = computed(()=>asset.currentLocationTypeID);

const selectedCategoryIDs = computed(()=>asset.selectedCategoryIDs);

watch([currentLocationTypeID, selectedCategoryIDs], async()=>{

    if(!currentLocationTypeID.value || selectedCategoryIDs.value.length === 0){

        console.log('no filter selected');
        
        return;
    }


    const params = asset.getIDsAsParams(asset.selectedCategoryIDs);

    const [assets, assetsByLocation] = await Promise.all([
        fetchAssetsAsGeoJSON(params), 
        fetchAssetsAsGeoJSONByLocationType(currentLocationTypeID.value, params )
    ]);


    if(assets.error.status){

        error.error = true;
        error.errorMessage = assets.error.message;

        return;

    }

    if(assetsByLocation.error.status){

        error.error = true;
        error.errorMessage = assetsByLocation.error.message;

    }

    asset.assetsGeoJSON = assets.data;

    asset.assetsAsGeoJSONByLocations = assetsByLocation.data;
    
});

</script>

<template>
    <div class="flex ml-96">
        <ul v-if="asset.assetsGeoJSON">
            <li
                v-for="asset in asset.assetsGeoJSON.features"
                :key="asset.properties.id"
                >
                <pre class="text-sm">{{asset.properties}}</pre>
            </li>
        </ul>
        <ul v-if="asset.assetsAsGeoJSONByLocations">
            <li
                v-for="asset in asset.assetsAsGeoJSONByLocations.features"
                :key="asset.properties.location_id"
                >
                <pre class="text-sm">{{asset.properties}}</pre>
            </li>
        </ul>
    </div>
</template>