<script lang="ts" setup>
import { fetchAssetsByCustomLocation } from '../../../services/fetch/fetch-assets';
import { useAssetsStore } from '../../../stores/assets';
import { Geometry } from 'geojson';

const asset = useAssetsStore();

const geometry:Geometry = {
    "type": "Polygon",
    "coordinates": [
      [
        [-74.01, 40.675],
        [-73.95, 40.675],
        [-73.95, 40.72],
        [-74.01, 40.72],
        [-74.01, 40.675]
      ]
    ]
};


async function handleCustomLocationClicked(){

    const params = asset.getIDsAsParams(asset.selectedCategoryIDs);
    
    const {data, error} = await fetchAssetsByCustomLocation(geometry, params);

    console.log(error);

    if(error.status){

      console.error(error.message);

    }

    console.log(data);
}


</script>

<template>
    <h2>Custom Location</h2>
    <button 
      @click="handleCustomLocationClicked"
      class="p-3 bg-gray-700 text-white"
      >Assets By Custom Location</button>
</template>