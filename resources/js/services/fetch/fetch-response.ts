import { FetchResponse } from '../../types/fetch';

export function generateFetchResponse():FetchResponse<null>{

    return {
        error: {
            status: false,
            message: ''
        },
        data: null
    }
}