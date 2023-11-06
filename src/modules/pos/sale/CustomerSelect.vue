<template>
  <el-form>
    <el-select v-model="selectedCustomer" @change="customerChange" placeholder="Select">
    <el-option
      v-for="customer in customers"
      :key="customer.id"
      :label="customer.first_name + ' ' + customer.last_name"
      :value="customer.id"
    />
  </el-select>
</el-form>

</template>

<script setup>
  import { ref } from 'vue'
  import { useSaleStateMachine } from "./saleStateMachine.js"

  const { state, send } = useSaleStateMachine()

  const props = defineProps(['customers'])
  const emit = defineEmits(['customerSelected'])

  //  refs

  const selectedCustomer = ref( null )

  //  methods

  const customerChange = () => {
    if( selectedCustomer && selectedCustomer.value > 0 ) {
      send('CUSTOMER_SELECTED')
      send('UNSELECT_SALE_GROUP')
      send('PRODUCT_SUBGROUP_UNSELECT')
      send('PRODUCT_UNSELECT')
      emit('customerSelected', selectedCustomer)
    }
  }



</script>