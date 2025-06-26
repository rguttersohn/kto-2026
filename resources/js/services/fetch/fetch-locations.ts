import { IndicatorData } from './../../types/indicators.d';
import {generateFetchResponse} from '../fetch/fetch-response';
import type { FetchResponse } from '../../types/fetch';

const BASE_URL = '/api/app/locations';

export async function fetchLocationIndicatorData(locationID: number, indicatorID: number, filtersAsParams:string): Promise<FetchResponse<IndicatorData[] | []>> {
    
    const fetchResponse = generateFetchResponse<IndicatorData[]>([]);

    let url = `${BASE_URL}/${locationID}/indicators/${indicatorID}/data`;

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