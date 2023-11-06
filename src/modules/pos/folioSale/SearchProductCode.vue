<template>
  <h2>Search Products by Product Code</h2>
  <el-form>
    <el-form-item label="Search Term">
      <el-input v-model="searchTerm"/>
      <el-button @click="search">Search</el-button>
    </el-form-item>
  </el-form>
</template>

<script setup>
  import { ref } from 'vue'
  import posData from './../data.js'

  const searchTerm = ref('')
  const products = ref([])

  const search = () => {
    const args = {
      search_term: searchTerm.value
    }
    posData.searchProductsByCode( args ).then( response => {
      console.table(response.data.products)
    }).catch( error => {
      //  bring in the composable error handler here
      console.log(error.message)
    })
  }
    

</script>