import { usePage } from '@inertiajs/vue3';
import { generateFetchResponse } from "./fetch-response";
import { FetchResponse } from '../../types/fetch';
import { Asset, AssetFeature, AssetsByLocation, AssetsByLocationFeature} from '../../types/assets';
import { useAssetsStore } from '../../stores/assets';

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

export async function fetchAssetsByLocationType(locationTypeID: number, categoryIDsAsParams:string | null): Promise<FetchResponse<AssetsByLocation[]>>{

    const page = usePage();

    const fetchResponse = generateFetchResponse<AssetsByLocation[]>([]);

    const url = new URL(`${page.props.origin}${BASE_URL}/aggregate`);    

    url.searchParams.set('by', 'location_type');
    url.searchParams.set('location_type', locationTypeID.toString());

    if (categoryIDsAsParams) {
        const searchParams = new URLSearchParams(categoryIDsAsParams);
        for (const [key, value] of searchParams.entries()) {
        url.searchParams.append(key, value);
        }
    }

    const response = await fetch(url.toString());

    const data = await response.json();

    if (!response.ok || data.error.status) {
        
        fetchResponse.error.status = true;
        fetchResponse.error.message = data.error.message;

        return fetchResponse;

      } 

    fetchResponse.data = data.data;

    return fetchResponse;

}

export async function fetchAssetsAsGeoJSONByLocationType(locationTypeID:number, categoryIDsAsParams:string | null): Promise<FetchResponse<AssetsByLocationFeature>>{
    
    const page = usePage();

    const fetchResponse = generateFetchResponse<AssetsByLocationFeature>({
        type: 'FeatureCollection',
        features: []
    });

    const url = new URL(`${page.props.origin}${BASE_URL}/aggregate`);    

    url.searchParams.set('by', 'location_type');
    url.searchParams.set('location_type', locationTypeID.toString());

    if (categoryIDsAsParams) {
        const searchParams = new URLSearchParams(categoryIDsAsParams);
        for (const [key, value] of searchParams.entries()) {
        url.searchParams.append(key, value);
        }
    }

    const response = await fetch(url.toString(), {
        headers:{
            'Content-Type': 'application/json',
            'Accept': 'application/geo+json'
        }
    });

    const data = await response.json();

    if (!response.ok || data.error.status) {
        
        fetchResponse.error.status = true;
        fetchResponse.error.message = data.error.message;

        return fetchResponse;

    } 

    fetchResponse.data = data.data;

    return fetchResponse;

}