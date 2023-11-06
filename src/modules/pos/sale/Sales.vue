<template>
  
  <div v-if="allDataLoaded">
  <!--
    <div>State:{{state.value}}</div>
    <div>customer:{{selectedCustomer}}</div>
    <div>group:{{selectedProductGroup}}</div>
    <div>subgroup:{{selectedSubgroup}}</div>
    <div>product:{{selectedProduct}}</div>
  -->
    <el-row>
      <el-col :xs="24" :sm="9">
        <h1>Select Item</h1>
        <div class="sale-select">
          <CustomerSelect
            :customers="customers"
            @customerSelected="selectCustomer"
          />
        </div>
        <div class="sale-select" v-if="state.matches('customerSelected')">
          <ProductGroupSelect
            :productGroups="productGroups"
            @productGroupSelected="selectProductGroup"
          />
        </div>
        <div class="sale-select" v-if="state.matches('customerSelected.productGroupSelected')">
          <ProductSubgroupSelect
            :productSubgroups="filteredProductSubgroups"
            @subgroupSelected="selectSubgroup"
          />
        </div>
        <div class="sale-select" v-if="state.matches('customerSelected.productGroupSelected.productSubgroupSelected')">
          <ProductSelect
            :products="filteredProducts"
            @productSelected="selectProduct"
            />
        </div>
        <div class="sale-select" v-if="selectedProduct && state.matches('customerSelected.productGroupSelected.productSubgroupSelected.productSelected')">
          <SaleDetail
            :customer="selectedCustomer"
            :product="selectedProductDetail"
            @cancelItem="cancelSaleItem"
            @addToSale="addToSale"
          />
        </div>
      </el-col>
      <el-col :xs="24" :sm="15">
        <SaleItemList
          :saleItems="saleItems"
          :paymentTypes="paymentTypes"
          @sale-items:remove-at-index="removeSaleItem"
          @post-sale="postSale"
        />
      </el-col>
    </el-row>
  </div>
</template>

<script setup>
  import { ref, computed, onMounted } from 'vue'
  import _ from 'lodash'
  import CustomerSelect from './CustomerSelect.vue'
  import ProductGroupSelect from './ProductGroupSelect.vue'
  import ProductSubgroupSelect from './ProductSubgroupSelect.vue'
  import ProductSelect from './ProductSelect.vue'
  import SaleDetail from './SaleDetail.vue'
  import SaleItemList from './SaleItemList.vue'
  import posData  from './../data.js'
  import { useSaleStateMachine } from "./saleStateMachine.js"


  const { state, send } = useSaleStateMachine()


  //  refs

  const initialPosDataLoaded = ref( false )
  const customersLoaded = ref( false )
  const customers = ref( [] )
  const paymentTypes = ref( null )
  const productGroups = ref ( null )
  const productSubgroups = ref( null )
  const products = ref( null )
  const taxGroups = ref( null )
  const taxTypes = ref( null )

  const selectedCustomer = ref( null )
  const selectedProductGroup = ref( null )
  const selectedSubgroup = ref( null )
  const selectedProduct = ref( null )

  const saleItems = ref([])

  //  computed

  const allDataLoaded = computed( () => {
    if(initialPosDataLoaded.value == true && customersLoaded.value == true){
      return true
    } else {
      return false
    }
  })

  const filteredProducts = computed( () => {
    if(selectedSubgroup){
      let fProducts = _.filter(products.value, o => {
        return o.product_subgroup == selectedSubgroup.value
      })
      return fProducts
    } else {
      return null
    }
  })

  const filteredProductSubgroups = computed( () => {
    if(selectedProductGroup.value){
      let fSubgroups = _.filter(productSubgroups.value, o => {
        return o.group_id == selectedProductGroup.value
      })
      return fSubgroups
    } else {
      return null
    }
  })

  const selectedProductDetail = computed( () => {
    if(selectedProduct && selectedProduct.value){
      const fProduct = {... _.find(products.value, o => {
        return o.id == selectedProduct.value
      })}
      // include tax group
      const taxGroup = {..._.find(taxGroups.value, o => {
        return o.id == fProduct.tax_group
      })}
      fProduct.tax_group_detail = taxGroup
      //  parse the string representation of tax_types
      fProduct.tax_group_detail.tax_types = JSON.parse(fProduct.tax_group_detail.tax_types)
      

      //  include an array of applicable tax types
      
      fProduct.tax_type_detail = []
      _.each(fProduct.tax_group_detail.tax_types, (value, key) => {
        const iTaxType = {..._.find(taxTypes.value, o => {
          return o.id == value
        })}
        fProduct.tax_type_detail.push(iTaxType)
      })
      
      //  don't make this reactive
      return _.cloneDeep(fProduct)
    } else {
      return null
    }
  })


  //  methods

  const addToSale = saleItem => {
    saleItems.value.push(saleItem)
    cancelSaleItem()

  }
  const cancelSaleItem = () => {
    selectedProductGroup.value = null
    selectedSubgroup.value = null
    selectedProduct.value = null
    send('PRODUCT_UNSELECT')
    send('PRODUCT_SUBGROUP_UNSELECT')
    send('UNSELECT_SALE_GROUP')
  }

  const getCustomers = () => {
    posData.getAllCustomers().then( response => {
      customersLoaded.value = true
      customers.value = response.data.customers
    }).catch( error => {
    })
  }

  const getInitialData = () => {
    posData.getPosData().then( response => {
      initialPosDataLoaded.value = true
      paymentTypes.value = response.data.payment_types
      productGroups.value = response.data.product_groups
      productSubgroups.value = response.data.product_subgroups
      products.value = response.data.products
      taxGroups.value = response.data.tax_groups
      taxTypes.value = response.data.tax_types

    }).catch( error => {
      //this.handleRequestError(error)
      //console.log('error:', error)
      console.log('request.status:', error.request.status)
      console.log('config:', error.comfig)
      console.log('message:', error.message)
      console.log('name:', error.name)
      console.log('code:', error.code)
      console.log('response:', error.response)
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
    posData.postSale( args ).then( response => {
      console.log(response.data)
    })
  }

  const removeSaleItem = ( index ) => {
    saleItems.value.splice(index, 1)
  }

  const selectCustomer = ( customerId ) => {
    selectedCustomer.value = customerId.value
    selectedProductGroup.value = null
    selectedSubgroup.value = null
    selectedProduct.value = null
    //  kill sale items with customer change
    saleItems.value = []
  }

  const selectProduct = ( productId ) => {
    selectedProduct.value = null
    selectedProduct.value = productId
  }

  const selectProductGroup = ( groupId ) => {
    selectedProductGroup.value = groupId
    //  if it's switching 
    selectedSubgroup.value = null
    selectedProduct.value = null
  }

  const selectSubgroup = ( subgroupId ) => {
    selectedSubgroup.value = subgroupId
    //  fix dependant variables
    selectedProduct.value = null
  }

  //  onMounted
  onMounted( () => {
    getCustomers()
    getInitialData()
    //  reset the state machine
    send('CUSTOMER_UNSELECT')
  })
</script>

<style scoped>
  .sale-select {
    padding: 4px;
    background-color: rgb(14, 14, 14);
    border-radius: 6px;
    margin-bottom: 4px;
    margin-right: 5px;
  }
</style>