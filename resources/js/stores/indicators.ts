import { defineStore } from 'pinia';
import type { Indicator } from '../types/indicators';
import { ref } from 'vue';


export const useIndicatorsStore = defineStore('indicators', () => {
  
  const indicator = ref<Indicator | null>(null);

  return { indicator };
}); 