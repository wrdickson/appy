<template>
  <el-button @click="postSale">Post Sale</el-button>
  <div>
    <div>Currency Code: {{ currencyCode }}</div>
    <div>Minor Units: {{ currencyMinorUnits }}</div>
    <div>activeSaleItem.price:{{ activeSaleItem.price }}</div>
    <div>priceIsValid:{{ priceIsValid }}</div>
  </div>
  <hr/>
  <div class="sale-form">
    <el-form
      :model="activeSaleItem"
      size="small"
    >
      <el-form-item label="Tax Group">
        <el-select v-model="activeSaleItem.taxGroup">
          <el-option v-for="taxGroup in taxGroups" :value="taxGroup.id" :label="taxGroup.title"></el-option>
        </el-select>
      </el-form-item>
      <el-form-item label="Quantity">
        <el-select v-model="activeSaleItem.quantity">
          <el-option value="1" :label="1"></el-option>
          <el-option value="2" :label="2"></el-option>
          <el-option value="3" :label="3"></el-option>
          <el-option value="4" :label="4"></el-option>
          <el-option value="5" :label="5"></el-option>
          <el-option value="6" :label="6"></el-option>
        </el-select>
      </el-form-item>
      <el-form-item label="Price">
        <el-input v-model="activeSaleItem.price"/>
      </el-form-item>
    </el-form>
  </div>
</template>

<script setup>
  import posData from './data.js'
  import { authStore } from './../../modules/auth/store.js'
  import { reactive, ref, computed, watch } from 'vue'
  import Dinero from 'dinero.js'



  //  CONSTANTS, for development . . . would be dynamic in real situation
  const currencyCode = 'CAD'
  const currencyMinorUnits = 2
  const quantities = [1,2,3,4,5,6,7,8,9,10]

  const taxGroups = [
    {
      id: 12,
      title: 'Daily Room Charge',
      taxTypes: [ 23, 93]
    },
    {
      id: 34,
      title: 'Monthly Rent',
      taxTypes: []
    },
    {
      id: 65,
      title: 'Alcohol',
      taxTypes: [ 23, 67 ]
    }
  ]

  const taxTypes = [
    {
      id: 23,
      title: 'VAT',
      rate: .0456
    },
    {
      id: 45,
      title: 'Sales Tax',
      rate: .0345
    },
    {
      id: 67,
      title: 'Alcohol Tax',
      rate: .0763
    },
    {
      id: 93,
      title: 'Room Tax',
      rate: .8765
    }
  ]

  //  SETUP

  //  we set currency AND precision when creating the objet
  Dinero.globalLocale = 'en-CA'
  let k5 = Dinero({ amount: 5050, currency: currencyCode, precision: currencyMinorUnits })
  console.log( 'toFormat(): ', k5.toFormat())
  console.log( 'toUnit()', k5.toUnit() )
  console.log('getLocale()', k5.getLocale())

  //  REFS
  const activeSaleItem = reactive({
    taxGroup: null,
    quantity: null,
    price: null
  })

  const priceIsValid = ref( false )
  const saleItems = ref([])


  //  COMPUTED
  const precision = computed( () => {
    return k5.getPrecision()
  })

  //  METHODS
  const postSale = () => {
    const args = {
      customer: 1,
      subtotal: 500,
      tax: 12,
      total: 512,
      sold_by: authStore().account.id
    }
    posData.postSale( args ).then( response => {
      console.log( response.data )
    })
  }

  const validatePriceInput = ( ( price, minorUnits, allowNegative = false) => {
    //  decimal values MUST have leading zero!
    let pattern = null
    switch( allowNegative ) {
      case true:
        switch ( minorUnits ) {
          case 0:
            //  zero minor units, no empty stirng, negative ok
            pattern = /^-?\d+$/
          break;
          case 2:
            //  two minor units, no empty string, negative ok
            pattern = /^-?\d+(\.\d{1,2})?$/
          break;
          case 3:
            //  three minor units, no empty string, negative ok
            pattern = /^-?\d+(\.\d{1,3})?$/
          break;
          default:
            //  always fails
            pattern = /$./
        }
      break;

      case false:
        switch ( minorUnits ) {
          case 0:
            //  zero minor units, no empty string, not negative
            pattern = /^\d+$/
          break;
          case 2:
            //  two minor units, no empty string, not negative
            pattern = /^\d+(\.\d{1,2})?$/
          break;
          case 3:
            //  three minor units, no empty string, not negative
            pattern = /^\d+(\.\d{1,3})?$/
          break;
          default:
            //  always fails
            pattern = /$./
        }
      break;
    }
    if(pattern){
      if( pattern.test(price) == false ) {
          return false
      } else {
        return true
      }
    } else {
      return false
    }

  })

  //  WATCHERS
  watch( () => activeSaleItem.price, (val) => {
    console.log('newVal:', val)
      let isValid = validatePriceInput( val, currencyMinorUnits, true )
      console.log('isValid:', isValid )
      priceIsValid.value = isValid
  })

</script>

<style scoped>
  .sale-form {
    font-size: 12px;
  }
</style>