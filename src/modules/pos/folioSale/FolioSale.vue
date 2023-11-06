<template>
  <h1>Folio Sale</h1>
  <SearchProductCode/>
</template>

<script setup>
  import {optionsStore} from './../../../modules/options/store.js'
  import SearchProductCode from './SearchProductCode.vue'


  const options = optionsStore()
  console.log(options.autoloadOptions)
  console.log(options.autoloadOptions.currency_code)

</script>

