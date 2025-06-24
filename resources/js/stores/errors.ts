import { defineStore } from "pinia";
import {ref} from 'vue';

export const useErrorStore = defineStore('errors', ()=>{

    const error = ref<boolean>();

    return {
        
        error
    }
})