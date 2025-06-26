import { FetchResponse } from '../../types/fetch';

export function generateFetchResponse<DataType>(defaultData: DataType): FetchResponse<DataType> {
    return {
      error: {
        status: false,
        message: ''
      },
      data: defaultData
    };
  }