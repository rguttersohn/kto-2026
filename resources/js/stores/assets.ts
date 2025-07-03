import { defineStore } from 'pinia';
import {shallowRef, ref, computed} from 'vue';
import { ParentCategory, AssetCategory } from '../types/assets';

export const useAssetsStore = defineStore('assets',()=>{

    const assetCategories = shallowRef<ParentCategory[] | null>(null);

    const selectedCategories = ref<AssetCategory[]>([]);

    const selectedCategoryIDs = computed(()=>selectedCategories.value.map(asset=>asset.id))


    return {
        assetCategories,
        selectedCategories,
        selectedCategoryIDs
    }
    
})