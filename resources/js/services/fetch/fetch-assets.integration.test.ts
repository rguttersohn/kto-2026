import { Geometry } from 'geojson';
import {vi, describe, it, expect } from 'vitest';
import { fetchAssetsAsGeoJSONByLocation } from './fetch-assets';
import { notNullish } from '@vueuse/core';

vi.mock('@inertiajs/vue3', async ()=>{
  return {
    usePage: vi.fn(()=>({
      props: {
        origin: 'http://localhost'
      }
    }))
  };
});

describe('fetchAssets', ()=>{

    it('fetches assets data', async()=>{

        const {fetchAssets} = await import('./fetch-assets');

        const params = '?filter[category][in][]=1&filter[category][in][]=3';
        
        const result = await fetchAssets(params);

        expect(result.error.status).toBe(false);

        const expectedKeys = [
            'description',
            'id'
        ];

        expect(Object.keys(result.data[0])).toEqual(expect.arrayContaining(expectedKeys));

    });

    it('returns error when missing category params', async ()=>{

        const {fetchAssets} = await import('./fetch-assets');

        const params = null;
        
        const result = await fetchAssets(params);

        expect(result.error.status).toBe(true);

    })

    it('returns error when param is malformed', async ()=>{

        const {fetchAssets} = await import('./fetch-assets');

        const params = '?filter[category][][]=1&filter[category][in][]=3';
        
        const result = await fetchAssets(params);

        expect(result.error.status).toBe(true);

    })

})

describe('fetchAssetsAsGeoJSON', ()=>{

    it('fetches assets as geojson', async()=>{

        const {fetchAssetsAsGeoJSON} = await import('./fetch-assets');

        const params = '?filter[category][in][]=1&filter[category][in][]=3';

        const result = await fetchAssetsAsGeoJSON(params);

        expect(result.error.status).toBe(false);

        const expectedKeys = [
            'description',
            'id',
            
        ];

        expect(Object.keys(result.data.features[0].properties)).toEqual(expect.arrayContaining(expectedKeys));

        
    })


    it('returns error when missing params', async()=>{

        const {fetchAssetsAsGeoJSON} = await import('./fetch-assets');

        const params = null;

        const result = await fetchAssetsAsGeoJSON(params);

        expect(result.error.status).toBe(true);
   
    })


    it('returns error when params malformed', async()=>{

        const {fetchAssetsAsGeoJSON} = await import('./fetch-assets');

        const params = '?filter[category][][]=1&filter[category][in][]=3';

        const result = await fetchAssetsAsGeoJSON(params);

        expect(result.error.status).toBe(true);
   
    })

})

describe('fetchAssetsByLocationType',()=>{

    it('returns assets by location type', async()=>{

        const {fetchAssetsByLocationType} = await import('./fetch-assets');

        const params = '?filter[category][in][]=1&filter[category][in][]=3';

        const result = await fetchAssetsByLocationType(1, params);

        expect(result.error.status).toBe(false);

         const expectedKeys = [
            'location_name',
            'location_id',
            'count'
        ];

        expect(Object.keys(result.data[0])).toEqual(expect.arrayContaining(expectedKeys));

        const expectedCountKeys = [
            'counts',
            'total'
        ]

       expect(Object.keys(result.data[0].count)).toEqual(expect.arrayContaining(expectedCountKeys));

    });

    it('returns error when missing params', async ()=>{

        const {fetchAssetsByLocationType} = await import('./fetch-assets');

        const params = null;

        const result = await fetchAssetsByLocationType(1, params);

        expect(result.error.status).toBe(true);

    });

     it('returns error when missing params', async ()=>{

        const {fetchAssetsByLocationType} = await import('./fetch-assets');

        const params = '?filter[category][][]=1&filter[category][in][]=3';

        const result = await fetchAssetsByLocationType(1, params);

        expect(result.error.status).toBe(true);

    });

})


describe('fetch assets by location type as geojson', ()=>{

    it('returns assets by location type as geojson', async()=>{

        const {fetchAssetsAsGeoJSONByLocationType} = await import('./fetch-assets');

        const params = '?filter[category][in][]=1&filter[category][in][]=3';

        const result = await fetchAssetsAsGeoJSONByLocationType(1, params);

        expect(result.error.status).toBe(false);

         const expectedKeys = [
            'location_name',
            'location_id',
            'count'
        ];

        expect(Object.keys(result.data.features[0].properties)).toEqual(expect.arrayContaining(expectedKeys));

        const expectedCountKeys = [
            'counts',
            'total'
        ]

       expect(Object.keys(result.data.features[0].properties.count)).toEqual(expect.arrayContaining(expectedCountKeys));

    })


    it('returns error when missing params', async ()=>{

        const {fetchAssetsAsGeoJSONByLocationType} = await import('./fetch-assets');

        const params = null;

        const result = await fetchAssetsAsGeoJSONByLocationType(1, params);

        expect(result.error.status).toBe(true);

    });

     it('returns error when missing params', async ()=>{

        const {fetchAssetsAsGeoJSONByLocationType} = await import('./fetch-assets');

        const params = '?filter[category][][]=1&filter[category][in][]=3';

        const result = await fetchAssetsAsGeoJSONByLocationType(1, params);

        expect(result.error.status).toBe(true);

    });

})


describe('fetchAssetsAsGeoJSONByLocation', ()=>{

    it('returns assets by location as geojson', async()=>{

        const {fetchAssetsAsGeoJSONByLocation} = await import('./fetch-assets');

        const params = '?filter[category][in][]=1&filter[category][in][]=3';

        const result = await fetchAssetsAsGeoJSONByLocation(1, params);

        expect(result.error.status).toBe(false);

        const expectedKeys = [
            'location_name',
            'location_id',
            'count'
        ];

        expect(Object.keys(result.data.features[0].properties)).toEqual(expect.arrayContaining(expectedKeys));

        const expectedCountKeys = [
            'counts',
            'total'
        ]

       expect(Object.keys(result.data.features[0].properties.count)).toEqual(expect.arrayContaining(expectedCountKeys));
    })


    it('returns error when missing params', async()=>{

        const {fetchAssetsAsGeoJSONByLocation} = await import('./fetch-assets');

        const params = null;

        const result = await fetchAssetsAsGeoJSONByLocation(1, params);

        expect(result.error.status).toBe(true);

    })

    it('returns error when param is malformed', async()=>{

        const {fetchAssetsAsGeoJSONByLocation} = await import('./fetch-assets');

        const params = '?filter[category][][]=1&filter[category][in][]=3';

        const result = await fetchAssetsAsGeoJSONByLocation(1, params);

        expect(result.error.status).toBe(true);

    })

})
