export interface AssetCategory {
    id: number, 
    name: string
}

export interface ParentCategory {
    id: number,
    group_name: string,
    subcategories: AssetCategory[]
}