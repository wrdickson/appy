<template>
  <div>
    <el-button 
      v-for="product in products"
      @click="selectProduct(product.id)" 
      color="#121212">
        {{product.product_title}}
    </el-button>
  </div>
</template>

<script setup>
  import { useSaleStateMachine } from "./saleStateMachine.js"
  const { state, send } = useSaleStateMachine()
  import { ref } from 'vue'

  const props = defineProps(['products'])
  const emit = defineEmits(['productSelected'])

  //  refs
  const selectedProduct = ref(null)

  //  methods
  const selectProduct = ( productId ) => {
    console.log('productId', productId)
    selectedProduct.value = productId
    emit('productSelected', selectedProduct.value )
    send('PRODUCT_SELECTED')
  }
</script>