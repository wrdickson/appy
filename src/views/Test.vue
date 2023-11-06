<template>
  <h1>Test</h1>
  <div>account: {{account.username}}</div>
  <div>{{jsonResponse}}</div>

  <el-input v-model="tText"></el-input>
  <div>{{tText}}</div>
  <el-button @click="testApi">Api Test</el-button>
  <el-button @click="doSomething">Do Something</el-button>
  <el-button @click="clearResponse">Clear</el-button>
  <hr/>
  <el-button @click="getSpaceTypes">Get Space Types</el-button>
  <el-button @click="getSpaceType">Get Space Type</el-button>
</template>

<script setup>

  import { computed, ref, onMounted, watch } from 'vue'
  import axios from 'axios'
  import { authStore } from '../modules/auth/store.js'
  import { useHandleRequestError } from '/src/composables/handleRequestError.js'

  //  code to execute when the view instantiates . . .

  //console.log(import.meta.env)

  //  refs

  const jsonResponse = ref('')
  const spaceTypes = ref('')
  const tText = ref('')

  //  computed
  const account = computed( ()=>{
    return authStore().account
  })

  const token = computed( ()=> {
    return authStore().token
  })

  //  methods
  const apiGetSpaceType = ()=> {
    const promise = axios({
      headers: {
        'Jwt': token.value
      },
      method: 'get',
      url: 'api/spacetypes/2'
    }).catch( error => {
      return error
    })
    return promise
  }

  const apiGetSpaceTypes = () => {
    const promise = axios({
      method: 'get',
      url: 'api/spacetypes'
    })
    return promise
  }
  
  const clearResponse = () => {
    jsonResponse.value = ''
  }

  const doSomething = () => {
    getDoSomething().then( response => {
      console.log( response )
      jsonResponse.value = JSON.stringify(response.data)
    })
  }

  const alertError = () => {
    console.log('error')
  }

  const getDoSomething = ()=>{
    const promise = axios({
      method: 'get',
      url: 'api/test/do-something'
    })
    return promise
  }

  const getSpaceType = () => {
   
      apiGetSpaceType()
      .then( response => {
        if( response.data ){
          console.log( response.data )
        } else {
          console.log( response.request.status)
          console.log( response.request.statusText)
          useHandleRequestError( response )
        }
      })
    
  }

  const getSpaceTypes = () => {
    apiGetSpaceTypes().then( response => {
      console.table(response.data)
      jsonResponse.value = JSON.stringify(response.data)
      spaceTypes.value = response.data
    })
  }

  const getTest = () => {
    const promise = axios({
      method: 'get',
      url: 'api/test'
    })
    return promise
  }

  const testApi = () => {
    console.log('testApi()')
    getTest().then( response => {
      console.log( response.data )
      jsonResponse.value = JSON.stringify(response.data)
    })
  }

  //  mounted

  onMounted(() => {
   document.title = 'Blavatsky'
  })

  //  watch

  watch( tText, ( newVal ) => {
    document.title = newVal
  })
  
</script>

<style scoped>
.el-notification {
  font-family: Verdana, Geneva, Tahoma, sans-serif;
}
</style>
