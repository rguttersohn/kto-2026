import { LocationType } from './../types/locations.d';
import { defineStore } from 'pinia';
import {shallowRef, ref, computed} from 'vue';
import { ParentCategory, AssetCategory, Asset, AssetFeature, AssetsByLocation, AssetsByLocationFeature } from '../types/assets';

export const useAssetsStore = defineStore('assets',()=>{

    const assetCategories = shallowRef<ParentCategory[] | null>(null);

    const selectedCategories = ref<AssetCategory[]>([]);

    const selectedCategoryIDs = computed(()=>selectedCategories.value.map(asset=>asset.id))

    const assets = shallowRef<Asset[] | null>(null);

    const assetsGeoJSON = shallowRef<AssetsByLocationFeature | null>(null);

    const currentLocationTypeID = ref<number | null>(1);

    const assetsByLocations = shallowRef<AssetsByLocation[] | null>(null);

    const assetsAsGeoJSONByLocations = shallowRef<AssetsByLocationFeature | null>(null);

    const assetLocationTypes = ref<LocationType[] | null>(null);

    function getIDsAsParams(selectedCategoryIDs:number[]):string{

        const params = new URLSearchParams();
       

        selectedCategoryIDs.forEach(id=>{
            
            params.append(`filter[category][in][]`, id.toString());

        })

        return params.toString();
    }

    return {
        assets,
        assetsGeoJSON,
        assetCategories,
        selectedCategories,
        selectedCategoryIDs,
        currentLocationTypeID,
        assetLocationTypes,
        assetsByLocations,
        assetsAsGeoJSONByLocations,
        getIDsAsParams
    }
    
})