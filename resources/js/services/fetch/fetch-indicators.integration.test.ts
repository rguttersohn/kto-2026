import {vi, describe, it, expect } from 'vitest';
import { fetchIndicatorData } from './fetch-indicators';

vi.mock('@inertiajs/vue3', async ()=>{
  return {
    usePage: vi.fn(()=>({
      props: {
        origin: 'http://localhost'
      }
    }))
  };
})

const indicatorID = 1;

const filterStrings = '?filter[timeframe][eq]=2020&filter[breakdown][eq]=21';

describe('fetchIndicatorData', () => {
  
  it('fetches data from real backend', async () => {
    
    const { fetchIndicatorData } = await import('./fetch-indicators')

    const result = await fetchIndicatorData(
      indicatorID,
      filterStrings,
      32,
      0
    )

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

  });

  it('returns error is true with bad request', async ()=>{

    const { fetchIndicatorData } = await import('./fetch-indicators')


    const result = await fetchIndicatorData(
      9999999,
      filterStrings,
      32,
      0
    );

    expect(result.error.status).toBe(true);

  })


})

describe('fetchIndicatorDataCount', async ()=>{


  it('returns data count', async ()=>{

    const { fetchIndicatorDataCount }  = await import ('./fetch-indicators');
    
    const result = await fetchIndicatorDataCount(
      indicatorID,
      filterStrings,
      false
    )

    expect(result.error.status).toBe(false);

    expect(result.data).toHaveProperty('count');
    
  });

  it('returns error with bad request', async ()=>{

    const { fetchIndicatorDataCount }  = await import ('./fetch-indicators');
    
    const result = await fetchIndicatorDataCount(
      9999999,
      filterStrings,
      false
    )

    expect(result.error.status).toBe(true);

  })

})


describe('fetchIndicatorGeoJSONData', async ()=>{

  it('returns geojson data', async ()=>{

    const { fetchIndicatorGeoJSONData } = await import ('./fetch-indicators');

    const result = await fetchIndicatorGeoJSONData(
      indicatorID,
      filterStrings,
      32,
      0,
      false
    );

    expect(result.error.status).toBe(false);

    expect(result.data).toHaveProperty('type');
    expect(result.data).toHaveProperty('features');

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

    expect(Object.keys(result.data.features[0].properties)).toEqual(expect.arrayContaining(expectedKeys));

  })


  it('returns error', async ()=>{

    const { fetchIndicatorGeoJSONData } = await import ('./fetch-indicators');

    const result = await fetchIndicatorGeoJSONData(
      999999,
      filterStrings,
      32,
      0,
      false
    );

    expect(result.error.status).toBe(true);

  })
  
  
})
