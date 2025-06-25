import { defineStore } from 'pinia';
import {shallowRef} from 'vue';

export const useAssetsStore = defineStore('assets',()=>{

    const assets = shallowRef(null);


    return {
        assets
    }
    
})