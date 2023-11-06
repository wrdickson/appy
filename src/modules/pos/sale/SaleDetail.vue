<template>
  <div>
<!--
    <div>Props:</div>
    <ul>
      <li>{{props.product.product_title}}</li>
      <li>price:{{props.product.price}}</li>
    </ul>
    <div>productCopy:</div>
    <ul>
      <li>product_title:{{productCopy.product_title}}</li>
      <li>price:{{productCopy.price}}</li>
    </ul>
-->
    <div class="input-card">
      <el-row>
        <el-col>
          <el-button style="float:right;" @click="cancelItem" type="danger">Cancel</el-button>
        </el-col>
      </el-row>
      <el-row>
        <el-col :xs="12" :sm="12">
          <div>Quantity:</div>
          <el-input v-model="quantity"/>
        </el-col>
        <el-col :xs="12" :sm="12">
          <div>Price:</div>
          <el-input disabled v-model="productCopyFormatPrice"/>
        </el-col>
      </el-row>
      <el-row>
        <el-col :xs="12" :sm="8">
          <div>Override Price:</div>
          <div v-if="priceIsChanged == false">
            <el-input v-model="overridePrice"/>
            <el-button v-if="overridePriceIsValid" @click="updateOverridePrice">override price</el-button>
          </div>
            <el-button v-if="priceIsChanged == true" @click="revertPrice">revert price</el-button>
        </el-col>
      </el-row>
      <el-row>
        <el-col>
          <el-button v-if="quantityIsValid" @click="addToSale" type="primary">Add To Sale</el-button>
        </el-col>
      </el-row>
      <el-row>
        <el-col>
          <table class="console-table">
            <tr>
              <td>Product</td><td>{{productCopy.product_title}}</td>
            </tr>
            <tr>
              <td>Quantity</td><td>{{quantity}}</td>
            </tr>
            <tr>
              <td>Major Unit Price</td><td>{{productCopy.price}}</td>
            </tr>
            <tr>
              <td>Format Price</td><td>{{productCopyFormatPrice}}</td>
            </tr>
            <tr>
              <td>quantityIsValid</td><td>{{quantityIsValid}}</td>
            </tr>
            <tr>
              <td>subtotal</td><td>{{subtotal}}</td>
            </tr>
            <tr>
              <td>Format Subtotal</td><td>{{subtotalFormat}}</td>
            </tr>
            <tr>
              <td>taxSpread</td><td>{{taxSpread}}</td>
            </tr>
            <tr>
              <td>taxTotal</td><td>{{taxTotal}}</td>
            </tr>
            <tr>
              <td>taxTotalFormat</td><td>{{taxTotalFormat}}</td>
            </tr>
            <tr>
              <td>total</td><td>{{total}}</td>
            </tr>
            <tr>
              <td>totalFormat</td><td>{{totalFormat}}</td>
            </tr>
          </table>
        </el-col> 
      </el-row>
    </div>
    
  </div>


  <el-row>
    <el-col :xs="12" :sm="12"></el-col>
      <el-form>

      </el-form>
    <el-col :xs="12" :sm="12">
      <!--
      <div>

        <el-form>
          <el-form-item label="Override Price">
            <el-input
              label="overridePrice"
              v-model="overridePrice"
            />
          </el-form-item>
        </el-form>
        <el-button @click="overrideSalePrice" v-if="overridePriceIsValid">Update Price to {{overridePriceFormat}}</el-button>
      </div>
    -->
    </el-col>
  </el-row>
</template>

<script setup>
  import { ref, reactive, computed, onMounted, watch } from 'vue'
  import Dinero from 'dinero.js'
  import _ from 'lodash'
  const props = defineProps(['customer', 'product'])
  const emit = defineEmits(['cancelItem', 'addToSale'])

  //  TODO --- refactor this
  const currencyMinorUnits = 2
  const currencyCode = 'USD'
  const roundingMode = 'HALF_UP'

  
  const productCopy = ref(_.cloneDeep(props.product))
  const productOriginal = ref(_.cloneDeep(props.product))

  const overridePrice = ref(null)
  const overridePriceFormat = ref(null)

  const quantity = ref(1)

  const priceIsChanged = ref(false)

  //  methods

  const addToSale = ( () => {
    const saleItem = {
      product: productCopy.value.id,
      productTitle: productCopy.value.product_title,
      quantity: parseInt(quantity.value),
      price: parseInt(productCopy.value.price),
      priceFormat: productCopyFormatPrice.value,
      subtotal: parseInt(subtotal.value),
      subtotalFormat: subtotalFormat.value,
      tax: parseInt(taxTotal.value),
      taxFormat: taxTotalFormat.value,
      taxSpread: taxSpread.value,
      total: parseInt(total.value),
      totalFormat: totalFormat.value

    }
    console.log(saleItem)
    emit('addToSale', saleItem)
  })

  const cancelItem = () => {
    emit('cancelItem')
  }

  const revertPrice = () => {
    productCopy.value = _.cloneDeep(productOriginal.value)
    overridePrice.value = null
    priceIsChanged.value = false
  }

  const updateOverridePrice = () => {
    productCopy.value.price = overridePriceMajorUnit
    //  clear the ref / input value
    priceIsChanged.value = true
    //  WTF!!!!! this just fucks everything up
    //overridePrice.value = null
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
  
  //  computed
  const productCopyFormatPrice = computed( () => {
    const fSalePrice = Dinero({ amount: productCopy.value.price, precision: currencyMinorUnits })
    switch( currencyMinorUnits ){
      case 3:
        return fSalePrice.toFormat('0.000')
      break;
      case 2:
        return fSalePrice.toFormat('0.00')
      break;
      case 0:
        return fSalePrice.toFormat('0')
      break;
    }
  })

  const overridePriceIsValid = computed( () => {
    //  exclude null
    if(overridePrice.value){
      return validatePriceInput( overridePrice.value, currencyMinorUnits, true )
    } else {
      return false
    }
  })
  
  const overridePriceMajorUnit = computed( () => {
    if(overridePriceIsValid.value == true){
      switch(currencyMinorUnits){
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

  const quantityIsValid = computed( () => {
    //  integer, not negative
    const pattern = /^\d+$/
    return pattern.test(quantity.value)
  })

  const subtotal = computed( () => {
    if(quantityIsValid.value){
      const s = Dinero({
        amount: productCopy.value.price,
        precision: currencyMinorUnits
      }).multiply(quantity.value).getAmount()
      return s
    } else {
      return 0
    }
    
  })

  const subtotalFormat = computed( () => {
    if(quantityIsValid.value){
      const fSubtotal = Dinero({ amount: parseInt(subtotal.value), precision: currencyMinorUnits })
      switch( currencyMinorUnits ){
        case 3:
          return fSubtotal.toFormat('0.000')
        break;
        case 2:
          return fSubtotal.toFormat('0.00')
        break;
        case 0:
          return fSubtotal.toFormat('0')
        break;
      }
    } else {
      return 0
    }
  })

  const taxSpread = computed( () => {
    let taxSpread = []
    let tax_total = 0
    _.each(productCopy.value.tax_type_detail, (tax_type) => {
      const d = Dinero({
        amount: subtotal.value,
        precision: currencyMinorUnits
      }).multiply(parseFloat(tax_type.rate), roundingMode).getAmount()

      taxSpread.push({
        i: tax_type.id.toString(),
        t: d.toString()
      })
    })
    return taxSpread
  })

  const taxTotal = computed( () => {
    let tax = 0
    _.each(productCopy.value.tax_type_detail, (tax_type) => {
      const d = Dinero({
        amount: subtotal.value,
        precision: currencyMinorUnits
      }).multiply(parseFloat(tax_type.rate), roundingMode).getAmount()

      tax = tax + d
    })
    return tax
  })

  
  const taxTotalFormat = computed( () => {
    const taxTotalF = Dinero({ amount: parseInt(taxTotal.value), precision: currencyMinorUnits })
    switch( currencyMinorUnits ){
      case 3:
        return taxTotalF.toFormat('0.000')
      break;
      case 2:
        return taxTotalF.toFormat('0.00')
      break;
      case 0:
        return taxTotalF.toFormat('0')
      break;
    }
  })

  const total = computed( () => {
    return subtotal.value + taxTotal.value
  })

  const totalFormat = computed( () => {
    const totalF = Dinero({ amount: parseInt(total.value), precision: currencyMinorUnits })
    switch( currencyMinorUnits ){
      case 3:
        return totalF.toFormat('0.000')
      break;
      case 2:
        return totalF.toFormat('0.00')
      break;
      case 0:
        return totalF.toFormat('0')
      break;
    }
  })
  
  //  methods

  const overrideSalePrice =  () => {
    salePrice.value = overridePriceMajorUnit.value
  }

  //  onMounted
  onMounted( () => {
  })

  //  watch
  watch( props, newVal => {
    productCopy.value = _.cloneDeep(newVal.product)
    productOriginal.value = _.cloneDeep(newVal.product)
    overridePrice.value = null
  })

</script>

<style scoped>
  .console-table {
    border-collapse: collapse;
    background-color: black;
  }

  .console-table td {
    padding: 2px;
    border: 1px solid rgb(171, 179, 171);
    font-size: 12px;
  }

  .input-card {
    padding: 4px;
    border-radius: 6px;
    background-color: #020122
  }
</style>