import { defineStore } from 'pinia';
import {ref} from 'vue';
import { Domain } from '../types/well-being';

export const useWellBeingStore = defineStore('well-being-store', ()=>{


    const currentDomain = ref<Domain | null>();

    return {
        currentDomain
    }
})
