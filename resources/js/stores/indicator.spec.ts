import { setActivePinia, createPinia } from 'pinia';
import { useIndicatorsStore } from './indicators';
import { beforeEach, describe, it } from 'vitest';

describe('useIndicatorStore', ()=>{


    it('converts filters to params', ()=>{

        beforeEach(()=>{

            const indicator = useIndicatorsStore();

            indicator.selectedFilters = [{
                id: crypto.randomUUID(),
                
            }]

            indicator.getFiltersAsParams()
        })
    })
})