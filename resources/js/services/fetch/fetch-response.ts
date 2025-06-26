import { FetchResponse } from '../../types/fetch';

export function generateFetchResponse():FetchResponse<any[]> {

    return {
        error: {
            status: false,
            message: ''
        },
        data: []
    }
}