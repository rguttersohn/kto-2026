export interface AssetCategory {
    id: number, 
    name: string
}

export interface ParentCategory extends AssetCategory {
    subcategories: AssetCategory[]
}