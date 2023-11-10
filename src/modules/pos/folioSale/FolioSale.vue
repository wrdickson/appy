<template>
  <el-row :gutter="10">
    <el-col :xs="24" :sm="12">
      <div v-if="paymentTypes && taxTypes">
        <h3>Folio Sale</h3>
        <SearchProductCode
          @selectProduct="selectProduct"
        />
        <SaleDetail
          v-if="selectedProduct"
          :product="selectedProduct"
          :taxTypes="_.cloneDeep(taxTypes)"
          @cancelItem="cancelSaleItem"
          @addToSale="addToSale"
          />
      </div>
    </el-col>
    <el-col :xs="24" :sm="12">
      <SaleItemList
        :saleItems="saleItems"
        :paymentTypes="paymentTypes"
        @sale-items:remove-at-index="removeSaleItem"
        @post-sale="postSale"
      />
    </el-col>
   </el-row>
</template>

<script setup>
  import {optionsStore} from '@modules/options/store.js'
  import SearchProductCode from './SearchProductCode.vue'
  import SaleDetail from './SaleDetail.vue'
  import SaleItemList from './SaleItemList.vue'
  import { ref, onMounted } from 'vue'
  import { useFolioSaleStateMachine } from "./folioSaleState.js"
  const { state, send } = useFolioSaleStateMachine()
  import folioSaleData from './data.js' 
  import _ from 'lodash'

  const selectedProduct = ref(null)
  const options = optionsStore()

  //  refs
  const paymentTypes = ref(null)
  const taxTypes = ref(null)

  const saleItems = ref([])


  //  methods
  const addToSale = saleItem => {
    saleItems.value.push(saleItem)
    cancelSaleItem()
  }

  const cancelSaleItem = () => {
    selectedProduct.value = null
  }

  const getPaymentTypes = () => {
    folioSaleData.getPaymentTypes().then( response => {
      console.log(response.data)
      paymentTypes.value = response.data.payment_types
    })
  }

  const getTaxTypes = () => {
    folioSaleData.getTaxTypes(). then( response => {
      console.log(response.data)
      taxTypes.value = response.data.tax_types
    })
  }

  const postSale = ( payments ) => {
    //  TODO . . . probably should validate some here

    //  TODO . . . using dummy data here for folio
    
    //  use _.cloneDeep to remove any reactivity
    const args = {
      folio: 1,
      sale_items: _.cloneDeep(saleItems.value),
      payments: _.cloneDeep(payments)
    }
    folioSaleData.postSale( args ).then( response => {
      console.log(response.data)
      selectedProduct.value = null
      saleItems.value = []
    }).catch( error => {
      console.log(error.message)
    })
  }

  const removeSaleItem = ( index ) => {
    saleItems.value.splice(index, 1)
  }

  const selectProduct = ( product ) => {
    selectedProduct.value = product
  }

  //  onMounted
  onMounted( () => {
    getTaxTypes()
    getPaymentTypes()
  })

</script>

