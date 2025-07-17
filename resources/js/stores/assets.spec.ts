import { beforeEach, describe, it, expect } from 'vitest';
import { useAssetsStore } from './assets';
import { setActivePinia, createPinia } from 'pinia';
import { useIndicatorsStore } from './indicators';


describe('getIDAsParams', ()=>{
    
    beforeEach(()=>{

        setActivePinia(createPinia());

    })

    it('returns a param string with filters', ()=>{

        const asset = useAssetsStore();

        const assetIDs = [1,3,4];

        const result = asset.getIDsAsParams(assetIDs);


        expect(decodeURIComponent(result)).toBe('filter[category][in][]=1&filter[category][in][]=3&filter[category][in][]=4');


    });

    it('returns an empty string when asset ids array is empty', ()=>{

        const asset = useAssetsStore();

        const assetIDs:Array<number> = [];

        const result = asset.getIDsAsParams(assetIDs);

        expect(decodeURIComponent(result)).toBe('');

    });
})