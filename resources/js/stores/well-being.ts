import { defineStore } from 'pinia';
import {ref} from 'vue';
import { Domain } from '../types/well-being';
import type { WellBeingFilter } from '../types/well-being';

export const useWellBeingStore = defineStore('well-being-store', ()=>{


    const currentDomain = ref<Domain | null>();

    const selectedFilters = ref<WellBeingFilter[]>([]);

      function generateFilterContainer(): WellBeingFilter {
        return {
          id: crypto.randomUUID(),
          filterName: {
            label: null,
            value: null
          },
          operator: {
            label: null,
            value: null
          },
          value: {
            label: null,
            value: null
          }
        };
      }

    return {
        currentDomain,
        selectedFilters,
        generateFilterContainer
    }
})
