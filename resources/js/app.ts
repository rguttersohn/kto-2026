import { createApp, h, DefineComponent } from 'vue'
import { createInertiaApp } from '@inertiajs/vue3'
import PrimeVue from 'primevue/config';

createInertiaApp({
  resolve: (name:string):DefineComponent => {
    const pages = import.meta.glob<DefineComponent>('./src/Pages/**/*.vue', { eager: true })
    return pages[`./src/Pages/${name}.vue`]
  },
  setup({ el, App, props, plugin }) {
    createApp({ render: () => h(App, props) })
      .use(plugin)
      .use(PrimeVue,{ unstyled: true })
      .mount(el)
  },
})