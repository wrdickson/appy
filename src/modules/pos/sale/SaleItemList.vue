<template>
  <div>
    <h1>Sale Items</h1>
    <el-table :data="saleItems" size="small" style="width:100%;">
      <el-table-column prop="productTitle" label="Item" width="170"/>
      <el-table-column prop="quantity" label="Qty"/>
      <el-table-column prop="priceFormat" label="Price"/>
      <el-table-column prop="subtotalFormat" label="Subtotal"/>
      <el-table-column prop="taxFormat"  label="Tax"/>
      <el-table-column prop="totalFormat" label="Total"/>
      <el-table-column width="50" fixed="right">
        <template #default="scope">
          <el-button @click="handleRemoveItem(scope)" type="danger" size="small">X</el-button>
        </template>
      </el-table-column>
    </el-table>
    <div>
      <table class="console-table">
        <tr><td>Subtotal</td><td>{{subtotal}}</td></tr>
        <tr><td>SubtotalFormat</td><td>{{subtotalFormat}}</td></tr>
        <tr><td>TaxTotal</td><td>{{taxTotal}}</td></tr>
        <tr><td>TaxTotalFormat</td><td>{{taxTotalFormat}}</td></tr>
        <tr><td>Total</td><td>{{total}}</td></tr>
        <tr><td>TotalFormat</td><td>{{totalFormat}}</td></tr>
      </table>
    </div>
    <Payments
      v-if="1"
      :paymentTypes="paymentTypes"
      :total="total"
      @post-sale="postSale"
    />
  </div>
</template>

<script setup>
  import _ from 'lodash'
  import{ computed } from 'vue'
  import Dinero from 'dinero.js'
  import Payments from './Payments.vue'
  const props = defineProps(['saleItems','paymentTypes'])
  const emit = defineEmits(['sale-items:remove-at-index', 'post-sale'])

  //  TODO --- refactor this
  const currencyMinorUnits = 2
  const currencyCode = 'USD'
  const roundingMode = 'HALF_UP'

  const handleRemoveItem = ( scope => {
    //console.log('scope', scope)
    //console.log('scope.$index', scope.$index)
    emit('sale-items:remove-at-index', scope.$index )
  })

  const subtotal = computed( () => {
    let subtotal = 0
    _.each(props.saleItems, saleItem => {
      subtotal += parseInt(saleItem.subtotal)
    })
    return subtotal
  })

  const subtotalFormat = computed( () => {
    const subtotalF = Dinero({ amount: parseInt(subtotal.value), precision: currencyMinorUnits })
    switch( currencyMinorUnits ){
      case 3:
        return subtotalF.toFormat('0.000')
      break;
      case 2:
        return subtotalF.toFormat('0.00')
      break;
      case 0:
        return subtotalF.toFormat('0')
      break;
    }
  })

  const taxTotal = computed( () => {
    let taxTotal = 0
    _.each(props.saleItems, saleItem => {
      taxTotal += parseInt(saleItem.tax)
    })
    return taxTotal
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
    let total = 0
    _.each(props.saleItems, saleItem => {
      total += parseInt(saleItem.total)
    })
    return total
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

  //  receive an emit from Payments, then emit it up to Sales
  const postSale = ( payments ) => {
    emit('post-sale', payments)
  }

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