import { fileURLToPath, URL } from 'node:url'

import { defineConfig } from 'vite'
import vue from '@vitejs/plugin-vue'

// https://vitejs.dev/config/
export default defineConfig({
  plugins: [
    vue(),
  ],
  resolve: {
    alias: {
      '@': fileURLToPath(new URL('./src', import.meta.url))
    }
  },
  server: {
    //  you DO have to proxy in a dev environment
    //  make sure this points to the dev root
    proxy: {
      '/api': 'http://localhost/appy'
      /*
      '/api' : {
        target: 'http://localhost/appy',
        changeOrigin: true,
        configure: (proxy, options) => {
          // proxy will be an instance of 'http-proxy'
        }
      }
      */
    }
  },
  base: ''
})
//  in order to install the app anywhere in the server directory, 
//  we do a couple of things:
//  1. router uses createWebHashHistory.  sorry, that just seems to be a limitation
//  2. base is set to an empty string as above
//  3. all api urls do NOT start with a slash : 'api/test', NOT '/api/test'
//  4. when building/deploying, the contents of the dist folder
//     go into whichever server subdirectory we are in
//     so . .  file structure looks like this:
//
//    index.html
//    > assets
//    > api
//
//  This way, the built app can be placed ANYWHERE on the deployment
//  server.  Of course, db config stuff needs to be configured correctly
//  either by an install script or a config file
