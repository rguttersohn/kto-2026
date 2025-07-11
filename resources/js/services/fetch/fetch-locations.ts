import { FetchResponse } from './../../types/fetch.d';
import { Location } from './../../types/locations.d';
import { IndicatorData, IndicatorFilters } from './../../types/indicators.d';
import {generateFetchResponse} from '../fetch/fetch-response';

const LOCATIONS_BASE_URL = '/api/app/locations';

const LOCATION_TYPES_BASE_URL= '/api/app/location-types';

export async function fetchLocationIndicatorData(locationID: number, indicatorID: number, filtersAsParams:string): Promise<FetchResponse<IndicatorData[] | []>> {
    
    const fetchResponse = generateFetchResponse<IndicatorData[]>([]);

    let url = `${LOCATIONS_BASE_URL}/${locationID}/indicators/${indicatorID}/data`;

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

    fetchResponse.data = data.data;

    return fetchResponse;

}

export async function fetchLocationIndicatorFilters(locationID: number, indicatorID: number): Promise<FetchResponse<IndicatorFilters>>{

    const fetchResponse = generateFetchResponse<IndicatorFilters>({
        timeframe:[],
        location_type:[],
        format:[],
        breakdown:[]
    })

    let url = `${LOCATIONS_BASE_URL}/${locationID}/indicators/${indicatorID}/filters`;

    const response = await fetch(url,{
        headers:{
            'Content-Type': 'application/json',
            'Accept': 'application/json'
        }
    });

    const data = await response.json();

    if (!response.ok) {
        
        fetchResponse.error.status = true;
        
        fetchResponse.error.message = data.error.message;

    }

    fetchResponse.data = data.data;

    return fetchResponse;
    
}

export async function fetchLocationsByType(locationTypeID: number): Promise<FetchResponse<Location[]>> {
    
    const fetchResponse = generateFetchResponse<Location[]>([]);

    const response = await fetch(`${LOCATION_TYPES_BASE_URL}/${locationTypeID}`,{
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

    fetchResponse.data = data.data.locations;

    return fetchResponse;

}