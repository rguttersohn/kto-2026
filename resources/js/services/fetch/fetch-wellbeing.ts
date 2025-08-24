import { generateFetchResponse } from '@services/fetch/generate-fetch-response';
import { Domain } from '../../types/well-being';

const BASE_URL = '/api/app/wellbeing';

export async function fetchWellBeingScoresByLocationType(locationTypeID: number){
    const fetchResponse = generateFetchResponse<Domain[]>([]);

    const response = await fetch(`${BASE_URL}/location-type/${locationTypeID}`, {
        headers: {
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