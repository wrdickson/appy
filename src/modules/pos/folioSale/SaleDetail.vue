<template>
  <div style="display:flex">
    <div style="margin-left: auto">
      <el-button type="danger" @click="cancel">Cancel</el-button>
    </div>
  </div>
  <table class="console-table">
    <tr>
      <th>Item</th>
      <th>Qty</th>
      <th>Price</th>
      <th>Subtotal</th>
      <th>Tax</th>
      <th>Total</th>
    </tr>
    <tr>
      <td>{{productCopy.product_title}}</td>
      <td>{{quantity}}</td>
      <td>{{productCopy.priceFormat}}</td>
      <td>{{subtotalFormat}}</td>
      <td>{{taxFormat}}</td>
      <td>{{totalFormat}}</td>
    </tr>
  </table>
  <el-row>
    <el-col :xs="12" :sm="12">
      <el-form label-position="top">
        <el-form-item label="Quantity">
          <el-input v-model="uQuantity" clearable/>
          <el-button v-if="uQuantityIsValid" @click="updateQuantity">Update Quantity</el-button>
        </el-form-item>
      </el-form>
    </el-col>
    <el-col :xs="12" :sm="12">
      <el-form label-position="top">
        <el-form-item label="Override Price">
          <el-input v-model="overridePrice"/>
          <el-button @click="changePrice" v-if="overridePriceIsValid">Change Price</el-button>
        </el-form-item>
      </el-form>
    </el-col>
  </el-row>
  <el-row>
    <el-col>
      <el-button type="primary" @click="addToSale">Add To Sale</el-button>
    </el-col>
  </el-row>
</template>

<script setup>

  import { ref, computed, watch } from 'vue'
  const props = defineProps(['product', 'taxTypes'])
  import _ from 'lodash'
  import Dinero from 'dinero.js'
  import { optionsStore } from './../../options/store.js'

  const emit = defineEmits(['cancelItem', 'addToSale'])

  const currencyMinorUnits = optionsStore().autoloadOptions.currency_fraction_digits
  const roundingMode = optionsStore().autoloadOptions.default_rounding_mode
  const productCopy = ref(_.cloneDeep(props.product))
  const quantity = ref(1)
  const uQuantity = ref(null)
  const overridePrice = ref(null)


  //  computed
  const overridePriceIsValid = computed( () => {
    //  exclude null
    if(overridePrice.value){
      return validatePriceInput( overridePrice.value, currencyMinorUnits, true )
    } else {
      return false
    }
  })

  const overridePriceMajorUnit = computed( () => {
    if(overridePrice.value && overridePriceIsValid.value == true){
      switch(parseInt(currencyMinorUnits)){
        case 3:
          const j3 = overridePrice.value * 1000
          const k3= j3.toFixed(0)
          //  cast to int for Dinero
          return parseInt(k3)
        break;
        case 2:
          const j2 = overridePrice.value * 100
          const k2 = j2.toFixed(0)
          //  cast to int for Dinero
          return parseInt(k2)
        break;
        case 0:
        //  cast to int for Dinero
          return parseInt(overridePrice.value) 
        break;
        default:
          return null
      }
    } else {
      return null
    }
  })

  const priceFormat = computed( () => {
    const subf = Dinero({
      amount: parseInt(productCopy.value.price),
      precision: parseInt(currencyMinorUnits)
    })
    return subf.toUnit().toFixed(currencyMinorUnits)
  })

  const uQuantityIsValid = computed( () => {
    //  integer, not negative
    const pattern = /^\d+$/
    return pattern.test(uQuantity.value)
  })

  const subtotal = computed( () => {
    return parseInt(quantity.value) * parseInt(productCopy.value.price)
  })

  const subtotalFormat = computed( () => {
    const subf = Dinero({
      amount: parseInt(subtotal.value),
      precision: parseInt(currencyMinorUnits)
    })
    return subf.toUnit().toFixed(currencyMinorUnits)
  })

  const taxDetail = computed( () => {
    let taxDetail = []
    _.each(productCopy.value.tax_types, tax_type => {
      let tt = _.find(props.taxTypes, o => {
        return o.id == tax_type
      })
      taxDetail.push(tt)
    })
    return taxDetail
  })

  const tax = computed( () => {
    let t = 0
    _.each( taxDetail.value, td => {
      const s = Dinero({
        amount: parseInt(subtotal.value)
      }).multiply(parseFloat(td.rate), roundingMode).getAmount()
      
      t = parseInt(t) + parseInt(s)
    })
    return t
  })

  const taxFormat = computed( () => {
    const v = Dinero({
      amount: parseInt(tax.value),
      precision: parseInt(currencyMinorUnits)
    }).toUnit().toFixed(currencyMinorUnits)
    return v
  })

  const taxSpread = computed( () => {
    const tSpread = []
    _.each( taxDetail.value, td => {
      const s = Dinero({
        amount: parseInt(subtotal.value),
        precision: parseInt(currencyMinorUnits)
      }).multiply(parseFloat(td.rate), roundingMode).getAmount()
      let a = {
        "i": td.id,
        "r": s
      }
      tSpread.push(a)
    })
    return tSpread
  })

  const total = computed( () => {
    return parseInt(subtotal.value) + parseInt(tax.value)
  })

  const totalFormat = computed( () => {
    const tf = Dinero({
      amount: parseInt(total.value),
      precision: parseInt(currencyMinorUnits)
    })
    return tf.toUnit().toFixed(currencyMinorUnits)
  })

  //  methods
  const addToSale = () => {
    const obj = {
      product: productCopy.value.id,
      productTitle: productCopy.value.product_title,
      quantity: parseInt(quantity.value),
      price: productCopy.value.price,
      priceFormat: priceFormat.value,
      subtotal: subtotal.value,
      subtotalFormat: subtotalFormat.value,
      tax: tax.value,
      taxFormat: taxFormat.value,
      total: total.value,
      totalFormat: totalFormat.value,
      taxSpread: taxSpread.value
    }
    console.log(obj)
    emit('addToSale', obj)

  }

  const cancel = () => {
    emit('cancelItem')
  }

  const changePrice = () => {
    productCopy.value.price = _.cloneDeep(overridePriceMajorUnit)
  }

  const updateQuantity = () => {
    quantity.value = uQuantity.value
  }

  const validatePriceInput = ( ( price, minorUnits, allowNegative = false) => {
    //  decimal values MUST have leading zero!
    let pattern = null
    switch( allowNegative ) {
      case true:
        switch ( parseInt(minorUnits) ) {
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
        switch ( parseInt(minorUnits) ) {
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

  watch( props, newVal => {
    productCopy.value = _.cloneDeep(newVal.product)
    quantity.value = 1
    uQuantity.value = null
    overridePrice.value = null
  })

</script>

<style scoped>
  .console-table {
    border-collapse: collapse;
    background-color: #1c1b22;
    margin-top: 5px;
    margin-bottom: 5px;
    width: 100%;
  }

  .console-table td, th {
    padding: 4px;
    border: 1px solid #4c4d4f;
    font-size: 14px;
  }

</style>