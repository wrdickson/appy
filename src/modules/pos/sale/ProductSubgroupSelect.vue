<template>
  <div>
    <el-button @click="selectSubgroup(productSubgroup.id)" color="#121212" v-for="productSubgroup in productSubgroups">
      {{productSubgroup.subgroup_title}}
    </el-button>
  </div>
</template>

<script setup>

  import { ref } from 'vue'
  import { useSaleStateMachine } from "./saleStateMachine.js"
  const { state, send } = useSaleStateMachine()
  const props = defineProps(['productSubgroups'])
  const emit = defineEmits(['subgroupSelected'])

  //  refs
  const selectedSubgroup = ref(null)

  //  methods
  const selectSubgroup = ( subgroupId ) => {
    console.log('subgroupId', subgroupId)
    selectedSubgroup.value = subgroupId
    send('PRODUCT_SUBGROUP_SELECTED')
    emit('subgroupSelected', selectedSubgroup.value)
  }
</script>