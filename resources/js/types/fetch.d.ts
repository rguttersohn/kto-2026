

export type FetchResponse<DataType> = {
    error: {
        status: boolean,
        message: string
    },
    data: DataType
}