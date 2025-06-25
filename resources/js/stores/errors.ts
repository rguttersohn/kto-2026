import { defineStore } from "pinia";
import {ref} from 'vue';

export const useErrorStore = defineStore('errors', ()=>{

    const error = ref<boolean>();

    const errorMessage = ref<string>('');

    return {
        
        error,
        errorMessage
    }
})