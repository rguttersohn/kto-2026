import { defineStore } from 'pinia';
import {shallowRef, ref, computed} from 'vue';
import { ParentCategory, AssetCategory, Asset, AssetFeature } from '../types/assets';

export const useAssetsStore = defineStore('assets',()=>{

    const assetCategories = shallowRef<ParentCategory[] | null>(null);

    const selectedCategories = ref<AssetCategory[]>([]);

    const selectedCategoryIDs = computed(()=>selectedCategories.value.map(asset=>asset.id))

    const assets = ref<Asset[] | null>(null);

    const assetsGeoJSON = ref<AssetFeature | null>(null);

    function getIDsAsParams(selectedCategoryIDs:number[]){

        let params = '';

        selectedCategoryIDs.forEach(id=>{
            
            params += `filter[category][in][]=${id}`;
        })

        return params;
    }

    return {
        assets,
        assetsGeoJSON,
        assetCategories,
        selectedCategories,
        selectedCategoryIDs,
        getIDsAsParams
    }
    
})