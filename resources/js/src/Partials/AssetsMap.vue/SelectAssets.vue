<script lang="ts" setup>
import { AssetCategory, ParentCategory } from '../../../types/assets';
import { ref, computed } from 'vue';
import { useAssetsStore } from '../../../stores/assets';


    const props = defineProps<{
        asset_categories: ParentCategory[]
    }>();

    const asset = useAssetsStore();

    const assetsDrawerOpen = ref<boolean>(false);

 
    function handleParentSelected(parent:ParentCategory){
  
        if(parent.subcategories.length !== 0){
           
            const match = isChildSelected(parent);

            if(!match){

                parent.subcategories.forEach(sub=>asset.selectedCategories.push(sub));

                return;
            }

            parent.subcategories.forEach(sub =>{

                const index = asset.selectedCategories.findIndex(asset=>asset.id === sub.id);

                asset.selectedCategories.splice(index, 1);
            })
            
            return;

        }

        if(!isSelected(parent.id)){

            asset.selectedCategories.push({
                id: parent.id,
                name: parent.group_name
            });

            return;

        }

        const index = asset.selectedCategories.findIndex(asset=>asset.id === parent.id);

        asset.selectedCategories.splice(index, 1);
        
    }

    function handleChildSelected(child: AssetCategory){
        
        if(isSelected(child.id)){

            const index = asset.selectedCategories.findIndex(asset=>asset.id === child.id);

            asset.selectedCategories.splice(index, 1);

            return;
        }

        asset.selectedCategories.push(child);
    }

    function isSelected(id: number):boolean{

       const isMatch = asset.selectedCategoryIDs.includes(id);
        
       return isMatch;
    }

    function isChildSelected(parent: ParentCategory):boolean{

        return parent.subcategories.some(sub=>asset.selectedCategoryIDs.includes(sub.id));
        
    }

    function areAllChildrenSelected(parent: ParentCategory):boolean{

        if(parent.subcategories.length === 0){

            return false;

        }

        let allChildrenSelected = true;

        parent.subcategories.forEach(sub=>{

            if(!isSelected(sub.id)){
                
                allChildrenSelected = false;
            
            } 
        })

        return allChildrenSelected;
    }

    function handleAssetClicked(assetButton: AssetCategory){

        if(isSelected(assetButton.id)){

            const index = asset.selectedCategories.findIndex(asset=> asset.id === assetButton.id );

            asset.selectedCategories.splice(index, 1);
        }

    }


</script>

<style>
.v-enter-active,
.v-leave-active {
  transition: opacity 0.5s ease;
}

.v-enter-from,
.v-leave-to {
  opacity: 0;
}
</style>

<template>
    <ul
        class="flex items-center gap-x-3 w-2/3 mx-auto h-10 p-3 overflow-x-auto whitespace-nowrap border-2 border-gray-700 rounded-lg"
        @click="assetsDrawerOpen = !assetsDrawerOpen"
        >
        <li
            v-for="category in asset.selectedCategories"
            :key="category.id"
            class="p-1 bg-gray-700 text-white"
            >
            <button @click="()=>handleAssetClicked(category)">{{ category.name }}</button>
        </li>
    </ul>
    <div class="relative w-2/3 mx-auto">
        <Transition>
            <ul
                v-if="assetsDrawerOpen"
                class="absolute w-full mx-auto h-48 overflow-y-auto p-3 border-2 border-gray-700 rounded-lg"
                >
                <li
                    v-for="parent in props.asset_categories"
                    :key="parent.id"
                    >
                    <label class="flex items-center gap-x-3">
                        <input
                            type="checkbox"
                            :checked="areAllChildrenSelected(parent)"
                            class="appearance-none size-4 border-[1px] border-gray-700 checked:bg-gray-700"
                            @change="()=>handleParentSelected(parent)"
                        >
                        <span>{{ parent.group_name }}</span>
                    </label>
                    <ul
                        v-if="parent.subcategories.length !== 0"
                        class="ml-5"
                        >
                        <li
                            v-for="sub in parent.subcategories"
                            :key="sub.id"
                            @change="handleChildSelected(sub)"
        
                            >
                            <label class="flex items-center gap-x-3">
                                <input
                                    type="checkbox"
                                    :checked="isSelected(sub.id)"
                                    class="appearance-none size-4 border-[1px] border-gray-700 checked:bg-gray-700"
                                >
                                <span>{{ sub.name }}</span>
                            </label>
                            </li>
                    </ul>
                </li>
            </ul>
        </Transition>
    </div>
</template>