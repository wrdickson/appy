import './assets/main.css'

import { createApp } from 'vue'
import { createPinia } from 'pinia'
import { createI18n } from 'vue-i18n'

import ElementPlus from 'element-plus'
import 'element-plus/dist/index.css'
import 'element-plus/theme-chalk/dark/css-vars.css'

import App from './App.vue'
import router from './router'
import _ from 'lodash'

const messages = {
  en: {
    message: {
      hello: 'hello'
    }
  },
  es: {
    message: {
      hello: 'hola'
    }
  }
}

const useI18n = createI18n({
  legacy: false, // you must set `false`, to use Composition API
  locale: 'en', // set locale
  fallbackLocale: 'en', // set fallback locale
  messages // set locale messages
})

//  module loader
import authRoutes from './modules/auth/routes.js'
_.each(authRoutes, route => { router.addRoute(route) })

import dashboardRoutes from './modules/dashboard/routes.js'
_.each(dashboardRoutes, route => { router.addRoute(route) })

import posRoutes from './modules/pos/routes.js'
_.each(posRoutes, route => { router.addRoute(route) })

import optionsRoutes from './modules/options/routes.js'
_.each(optionsRoutes, route => { router.addRoute(route) }) 


const app = createApp(App)
app.use(useI18n)
app.use(createPinia())
app.use(router)
app.use(ElementPlus)
app.mount('#app')
