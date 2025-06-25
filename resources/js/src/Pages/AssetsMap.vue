<script lang="ts" setup>
import {ref} from 'vue';
import { ParentCategory } from '../../types/assets';
import { LocationType } from '../../types/locations';
import AppLayout from '../Layouts/AppLayout.vue';

defineOptions({
    layout: AppLayout
})

const props = defineProps<{
    asset_categories: ParentCategory[],
    location_types: LocationType[]
}>();

const currentCategoryIDs = ref<number[]>([]);

function handleCategoryClick(event:Event){

    const target = event.target as HTMLButtonElement;

    const id = target.dataset.categoryId;

    if(!id){
        return;
    }

    currentCategoryIDs.value.push(parseInt(id));

}

function isSelectedCategory(id:number):boolean{

    return currentCategoryIDs.value.includes(id);
}


const currentSubcategoryIDs = ref<number[]>([]);


function handleSubcategoryClick(event:Event){
    
    const target = event.target as HTMLButtonElement;

    const id = target.dataset.subcategoryId;

    if(!id){
        return;
    }
    
    currentSubcategoryIDs.value.push(parseInt(id))

}

function isSelectedSubcategory(id:number):boolean{

    return currentSubcategoryIDs.value.includes(id);
}



</script>

<template>

<h1>Community Resources</h1>
<section>
    <h2>Select a Resource</h2>
    <ul
        @click="handleCategoryClick"
    >
         <li
            v-for="{id, name, subcategories} in props.asset_categories"
            :key="id"
            >
                <button
                    :data-category-id="id"
                    :class="['w-fit',{
                            'bg-black text-white' : isSelectedCategory(id),
                        }]"
                >{{ name }}</button>
                <ul @click.stop="handleSubcategoryClick">
                    <li 
                        v-for="{id, name} in subcategories"
                        :key="id"
                        :class="['w-fit ml-3',{
                            'bg-black text-white' : isSelectedSubcategory(id)
                        }]"
                        >
                        <button
                        :data-subcategory-id="id"
                        >{{ name }}</button>
                    </li>
                </ul>
            </li>
    </ul>
</section>
<section>
    <h2>Select a Location Type</h2>
    <ul>
        <li 
            v-for="{id, plural_name} in props.location_types"
            :key="id"
            >
            <button>{{ plural_name }}</button>
        </li>
    </ul>
</section>

</template>