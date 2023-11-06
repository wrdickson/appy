<template>
  <h1>Payments</h1>
  <el-table
    :data="payments"
    size="small"
    style="width: 100%"
  >
    <el-table-column prop="paymentType" label="PtypeId"/>
    <el-table-column prop="paymentTypeName" label="Payment Type"/>
    <el-table-column prop="amountMajorUnit" label="Amount M Unit"/>
    <el-table-column prop="amountFormat" label="Amount Format"/>
    <el-table-column width="50" fixed="right">
        <template #default="scope">
          <el-button @click="handleRemoveItem(scope)" type="danger" size="small">X</el-button>
        </template>
      </el-table-column>
  </el-table>

  <el-button @click="showAddPayment = true">Add Payment</el-button>
  <div v-if="showAddPayment">
    <div style="display:flex;">
      <el-button type="danger" @click="closeAddPayment" style="margin-left:auto;">Close</el-button>
    </div>
    <div>
      <el-select v-model="selectedPaymentType">
        <el-option v-for="paymentType in paymentTypes"
          :key="paymentType.id"
          :label="paymentType.payment_title"
          :value="paymentType.id"
        />
      </el-select>
      <div>Amount:</div>
      <el-input v-model="paymentAmount"/>
      <el-button @click="applyPayment" v-if="paymentAmountIsValid && paymentAmount">Apply Payment</el-button>
    </div>
  </div>
  <div>
    <el-button v-if="paymentsEqualSaleAmount" @click="postPayment">
      Post Payment
    </el-button>
  </div>
  <div v-if="debug" class="console-table">
    <table>
      <tr><td>total -prop from SaleItemList-</td><td>{{total}}</td></tr>
      <tr><td>paymentsTotal</td><td>{{paymentsTotal}}</td></tr>
      <tr><td>paymentsEqualSaleAmount</td><td>{{paymentsEqualSaleAmount}}</td></tr>
      <tr><td>.</td><td>.</td></tr>
      <tr><td>selectedPaymentType</td><td>{{selectedPaymentType}}</td></tr>
      <tr><td>selectedPaymentTypeName</td><td>{{selectedPaymentTypeName}}</td></tr>
      <tr><td>paymentAmount</td><td>{{paymentAmount}}</td></tr>
      <tr><td>paymentAmountIsValid</td><td>{{paymentAmountIsValid}}</td></tr>
      <tr><td>paymentAmountMajorUnit</td><td>{{paymentAmountMajorUnit}}</td></tr>
      <tr><td>paymentAmountFormat</td><td>{{paymentAmountFormat}}</td></tr>
    </table>
  </div>
</template>

<script setup>
  import{ computed, ref } from 'vue'
  import _ from 'lodash'
  import Dinero from 'dinero.js'
  const props = defineProps(['paymentTypes', 'total'])
  const emit = defineEmits(['post-sale', 'payments:remove-at-index'])

  //  TODO --- refactor this
  const currencyMinorUnits = 2
  const currencyCode = 'USD'
  const roundingMode = 'HALF_UP'

  //  refs
  const showAddPayment = ref(false)
  const selectedPaymentType = ref(null)
  const paymentAmount = ref(null)
  const payments = ref([])


  const debug = ref(true)

  //  methods
  const applyPayment = () => {
    const payment = {
      paymentType: selectedPaymentType.value,
      paymentTypeName: selectedPaymentTypeName.value,
      amountMajorUnit: paymentAmountMajorUnit.value,
      amountFormat: paymentAmountFormat.value
    }
    payments.value.push(payment)
    //  clear out values
    selectedPaymentType.value = null
    paymentAmount.value = null
    showAddPayment.value = false
  }

  const closeAddPayment = () => {
    showAddPayment.value = false
    selectedPaymentType.value = null
    paymentAmount.value = null
  }

  const handleRemoveItem = scope => {
    emit('payments:remove-at-index', scope.$index )
    console.log('payments', payments.value)
    payments.value.splice( scope.$index, 1 )
  }

  //  emit this up to SaleItemList, which will pass it up to Sales
  const postPayment = () => {
    emit('post-sale', payments.value)
  }


  //  computed

  const paymentAmountFormat = computed( () => {
    if(paymentAmountIsValid.value && paymentAmountMajorUnit) {
      const fPaymentAmount = Dinero({ amount: parseInt(paymentAmountMajorUnit.value), precision: currencyMinorUnits })
      switch( currencyMinorUnits ){
        case 3:
          return fPaymentAmount.toFormat('0.000')
        break;
        case 2:
          return fPaymentAmount.toFormat('0.00')
        break;
        case 0:
          return fPaymentAmount.toFormat('0')
        break;
      }
    } else {
      return null
    }
  })

  const paymentAmountMajorUnit = computed( () => {
    if(paymentAmountIsValid.value == true){
      switch(currencyMinorUnits){
        case 3:
          const j3 = paymentAmount.value * 1000
          const k3= j3.toFixed(0)
          //  cast to int for Dinero
          return parseInt(k3)
        break;
        case 2:
          const j2 = paymentAmount.value * 100
          const k2 = j2.toFixed(0)
          //  cast to int for Dinero
          return parseInt(k2)
        break;
        case 0:
        //  cast to int for Dinero
          return parseInt(paymentAmount.value) 
        break;
        default:
          return null
      }
    } else {
      return null
    }
  })
  const paymentAmountIsValid = computed( () => {
    //  exclude null
    if(paymentAmount.value){
      return validatePriceInput( paymentAmount.value, currencyMinorUnits, true )
    } else {
      return false
    }
  })

  const paymentsEqualSaleAmount = computed( () => {
    if( props.total == paymentsTotal.value ){
      return true
    } else {
      return false
    }
  })

  const paymentsTotal = computed( () => {
    let pTotal = 0
    _.each(payments.value, payment => {
      pTotal += parseInt(payment.amountMajorUnit)
    })
    return pTotal
  })

  const selectedPaymentTypeName = computed( ()=> {
    
    if(selectedPaymentType.value){
      const sel = _.find(props.paymentTypes, o => {
        return o.id == selectedPaymentType.value
      })
      return sel.payment_title
    } else {
      return null
    }
  })

  //  methods
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
</style>