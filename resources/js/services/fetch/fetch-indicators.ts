import { IndicatorData, IndicatorFeature } from './../../types/indicators.d';
import { generateFetchResponse } from "./fetch-response";
import { FetchResponse } from '../../types/fetch';
import { usePage } from '@inertiajs/vue3';

const BASE_URL = '/api/app/indicators'

export async function fetchIndicatorData(
    indicatorID: number,
    filtersAsParams: string | null,
    limit?: number,
    offset?: number
  ): Promise<FetchResponse<IndicatorData[]>> {
  
    const page = usePage();

    const fetchResponse = generateFetchResponse<IndicatorData[]>([]);
  
    const url = new URL(`${page.props.origin}${BASE_URL}/${indicatorID}/data`);
  
    // Apply filters if present
    if (filtersAsParams) {
      const searchParams = new URLSearchParams(filtersAsParams);
      url.search = searchParams.toString();
    }
  
    // Add limit and offset if defined
    if (typeof limit === 'number') {
      url.searchParams.set('limit', limit.toString());
    }
  
    if (typeof offset === 'number') {
      url.searchParams.set('offset', offset.toString());
    }
  
    const response = await fetch(url.toString(), {
      headers: {
        'Content-Type': 'application/json',
        'Accept': 'application/json'
      }
    });
  
    const responseData = await response.json();
  
    if (!response.ok) {
      fetchResponse.error.status = true;
      fetchResponse.error.message = responseData.error?.message ?? 'Unknown error';
      return fetchResponse;
    }
  
    fetchResponse.data = responseData.data;
    
    return fetchResponse;

}
  
export async function fetchIndicatorGeoJSONData(indicatorID:number, filtersAsParams:string | null): Promise<FetchResponse<IndicatorFeature>> {
    
    const fetchResponse = generateFetchResponse<IndicatorFeature>({
        type: "FeatureCollection",
        features: []
    });

    let url = `${BASE_URL}/${indicatorID}/data`;

    if(filtersAsParams){

        url += `?${filtersAsParams}`;

    }

    const response = await fetch(url,{
        headers:{
            'Content-Type': 'application/json',
            'Accept': 'application/geo+json'
        }
    });

    if (!response.ok) {
    
        fetchResponse.error.status = true;

        const responseData = await response.json();
        
        fetchResponse.error.message = responseData.error.message;

    }

    const responseData = await response.json();

    fetchResponse.data = responseData.data as IndicatorFeature;

    return fetchResponse;
}
