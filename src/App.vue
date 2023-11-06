<template>
  <div v-if="authCompleted && optionsLoaded" class="container">
    <div class="head" style="display: flex;">
        <RouterLink to="/">Home</RouterLink>
        <RouterLink to="/folio-sale">Folio Sale</RouterLink>
        <RouterLink to="/sales">Sales</RouterLink>
        <!--<RouterLink to="/test">Test</RouterLink>-->
        <RouterLink v-if="account.permission > 3" to="/dashboard">Dashboard</RouterLink>
        <!--<RouterLink v-if="account.permission > 3" to="/postSale">Post Sale</RouterLink>-->
        <RouterLink style="margin-left:auto;" v-if="account.id < 1" to="/login">Login</RouterLink>
        <RouterLink style="margin-left:auto;" v-if="account.id > 0" to="/logoff">{{account.username}} - Logoff</RouterLink>
    </div>
    <div class="main">
      <RouterView />
    </div>
  </div>
</template>

<script setup>
  import { RouterLink, RouterView, useRouter } from 'vue-router'
  import { ref } from 'vue'

  //  modules/auth
  import { authStore } from './modules/auth/store.js'
  import authData from './modules/auth/data.js'

  //  modules/options
  import { optionsStore } from './modules/options/store.js'
  import optionsData from './modules/options/data.js'
  
  import { computed } from 'vue'

  //  refs
  const authCompleted = ref( false )
  const optionsLoaded = ref( false )

  const router = useRouter()

  //  handle account(user) details
  
  const localstorageAccount = JSON.parse(localStorage.getItem('account'))
  const token = localStorage.getItem('token')
  
  if (localstorageAccount && token ) {
    authData.authorizeToken( token ).then( (response) => {
      if(response.data.decoded.account){
        authStore().setAccount( response.data.decoded.account )
        authStore().setToken( token )
      }
      authCompleted.value = true
    }).catch( err => {
      authStore().setAccountToGuest()
      //router.push('/Login')
      authCompleted.value = true
    })
  } else {
    authStore().setAccountToGuest()
    //router.push('/Login')
    authCompleted.value = true
  }

  const account = computed( () => {
    return authStore().account
  })

  //  load options
  optionsData.getAutoloadOptions().then( response => {
    console.log(response)
    console.log( optionsStore() )
    optionsStore().setAutoloadOptions(response.data.options)
    optionsLoaded.value = true
  })

</script>


<style>
body {
  margin: 0px;
  font-family: Verdana, Geneva, Tahoma, sans-serif;

}

.container {
  margin-top: 5px;
  margin-left: 5px;
  margin-right: 5px;
}

.head {
  height: 32px;
  padding: 1px;
  
}
.head a:link, a:visited {
  text-decoration: none;
  color: #fbfbfe;
  font-size: 16px;
  padding: 4px;
  margin-right: 3px;
  border: 1px solid #8b8b8b;
  border-radius: 4px;
}
.head a:hover {
  background-color: rgb(136, 141, 209);
}

.main {
  
}
</style>
