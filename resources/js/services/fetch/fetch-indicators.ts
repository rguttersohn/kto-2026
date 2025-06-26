import { IndicatorData, IndicatorFeature } from './../../types/indicators.d';
import { generateFetchResponse } from "./fetch-response";
import { FetchResponse } from '../../types/fetch';

const BASE_URL = '/api/app/indicators'

export async function fetchIndicatorData( indicatorID:number, filtersAsParams:string | null): Promise<FetchResponse<IndicatorData[]>> {
    
    const fetchResponse = generateFetchResponse<IndicatorData[]>([]);

    let url = `${BASE_URL}/${indicatorID}/data`;

    if(filtersAsParams){
        url += `?${filtersAsParams}`;
    }

    const response = await fetch(url,{
        headers:{
            'Content-Type': 'application/json',
            'Accept': 'application/json'
        }
    });

    if (!response.ok) {
        
        fetchResponse.error.status = true;

        const data = await response.json();
        
        fetchResponse.error.message = data.error.message;

    }

    const data = await response.json();

    fetchResponse.data = data;

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