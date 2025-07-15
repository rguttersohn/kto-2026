import {vi, describe, expect, it} from 'vitest';

vi.mock('@inertiajs/vue3', async ()=>{
  return {
    usePage: vi.fn(()=>({
      props: {
        origin: 'http://localhost'
      }
    }))
  };
});

const indicatorID = 1;

const filterStrings = '?filter[timeframe][eq]=2020&filter[breakdown][eq]=21';


describe('fetchLocationIndicatorData',()=>{

    it('test indicator data by location', async ()=>{

        const {fetchLocationIndicatorData} = await import('./fetch-locations');

        const result = await fetchLocationIndicatorData(1,indicatorID, filterStrings);

        expect(result.error.status).toBe(false);

        const expectedKeys = [
        'data',
        'indicator_id',
        'location_id',
        'location',
        'location_type_id',
        'location_type',
        'timeframe',
        'breakdown',
        'format'
      ]

        expect(Object.keys(result.data[0])).toEqual(expect.arrayContaining(expectedKeys));

    })

    it('test return error when location id is not found', async ()=>{

        const {fetchLocationIndicatorData} = await import('./fetch-locations');

        const result = await fetchLocationIndicatorData(99999,1, filterStrings);

        expect(result.error.status).toBe(true);


    })



     it('test return error when indicator id is not found', async ()=>{

        const {fetchLocationIndicatorData} = await import('./fetch-locations');

        const result = await fetchLocationIndicatorData(1,9999, filterStrings);

        expect(result.error.status).toBe(true);

    })
})


