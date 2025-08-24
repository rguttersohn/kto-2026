import { defineStore } from 'pinia';
import {ref, shallowRef } from 'vue';
import { Domain } from '../types/well-being';
import type { LocationDomain, WellBeingFilter } from '../types/well-being';
import { LocationType } from '../types/locations';

export const useWellBeingStore = defineStore('well-being-store', ()=>{

    const currentDomain = ref<Domain | null>();

    const domainScoresByLocation = shallowRef<LocationDomain[] | null>(null);

    const currentLocationType = ref<LocationType | null>(null);

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
        currentLocationType,
        selectedFilters,
        domainScoresByLocation,
        generateFilterContainer
    }
})
