<template>
  <div>
    <el-button color="#121212" v-for="productGroup in productGroups" @click="selectGroup(productGroup.id)">
      {{productGroup.group_title}}
    </el-button>
  </div>
</template>

<script setup>
  import { ref } from 'vue'
  import { useSaleStateMachine } from "./saleStateMachine.js"

  const { state, send } = useSaleStateMachine()
  const props = defineProps(['productGroups'])
  const emit = defineEmits(['productGroupSelected'])

  //  refs
  const selectedProductGroup = ref( null )

  //  methods

  const selectGroup = (productGroupId) => {
    selectedProductGroup.value = productGroupId
    send('PRODUCT_GROUP_SELECTED')
    send('PRODUCT_UNSELECT')
    send('PRODUCT_SUBGROUP_UNSELECT')
    emit('productGroupSelected', selectedProductGroup.value)
  }

</script>
