import { usePage } from '@inertiajs/vue3';
import { generateFetchResponse } from "./fetch-response";
import { FetchResponse } from '../../types/fetch';
import { Asset, AssetFeature} from '../../types/assets';


const BASE_URL = '/api/app/assets';

export async function fetchAssets(categoryIDsAsParams:string | null):Promise<FetchResponse<Asset[]>>{

    const page = usePage();

    const fetchResponse = generateFetchResponse<Asset[]>([]);

    const url = new URL(`${page.props.origin}${BASE_URL}`);

    if(categoryIDsAsParams){
        const searchParams = new URLSearchParams(categoryIDsAsParams);
        url.search = searchParams.toString();
    }

    const response = await fetch(url);

    if(!response.ok){
        
        fetchResponse.error.status = true;

        const errorData = await response.json();

        fetchResponse.error.message = errorData.error.message;

        return fetchResponse;
    }

    const responseData = await response.json();

    fetchResponse.data = responseData.data;
    
    return fetchResponse;
}

export async function fetchAssetsAsGeoJSON(categoryIDsAsParams:string | null):Promise<FetchResponse<AssetFeature>>{

    const page = usePage();

    const fetchResponse = generateFetchResponse<AssetFeature>({
        type: 'FeatureCollection',
        features: []
    });

    const url = new URL(`${page.props.origin}${BASE_URL}`);

    if(categoryIDsAsParams){
        const searchParams = new URLSearchParams(categoryIDsAsParams);
        url.search = searchParams.toString();
    }

    const response = await fetch(url, {
        headers:{
            'Content-Type': 'application/json',
            'Accept': 'application/geo+json'
        }
    });

    if(!response.ok){
        
        fetchResponse.error.status = true;

        const errorData = await response.json();

        fetchResponse.error.message = errorData.error.message;

        return fetchResponse;
    }

    const responseData = await response.json();

    fetchResponse.data = responseData.data;
    
    return fetchResponse;
}