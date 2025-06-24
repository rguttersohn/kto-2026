import { defineStore } from 'pinia';
import { ref } from 'vue';


export const useIndicatorsStore = defineStore('indicators', () => {
  const indicator = ref<Record<string, any>>({});

  return { indicator };
}); 