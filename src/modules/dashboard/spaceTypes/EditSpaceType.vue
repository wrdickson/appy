<template>
  EditSpaceType
  <el-button @click="send('SPACE_TYPE_LIST')">Back</el-button>
  <el-form>
    <el-form-item label="Title">
      <el-input v-model="iSpaceType.title"/>
    </el-form-item>
    <el-form-item label="Is Active">
      <el-switch active-value="1" inactive-value="0" v-model="iSpaceType.is_active"/>
    </el-form-item>
    <el-form-item label="Display Order">
      <el-input v-model="iSpaceType.display_order"/>
    </el-form-item>
    <el-form-item>
      <el-button @click="save">Save</el-button>
      <el-button @click="revert">Revert</el-button>
    </el-form-item>
  </el-form>
</template>

<script setup>
  import { useSpaceTypeMachine } from './spaceTypeStateMachine.js'
  import spaceTypesData  from './data.js'
  import { ref } from 'vue'
  import { ElMessage } from 'element-plus'
  //  props
  const props = defineProps(['spaceType'])
  const emit = defineEmits(['reload'])

  //  setup code

  //  expose state machine functions
  const { state, send } = useSpaceTypeMachine()

  //  refs
  const initialData = ref({
    id: props.spaceType.id,
    title: props.spaceType.title,
    //  switch control needs a string to be responsive
    is_active: props.spaceType.is_active.toString(),
    display_order: props.spaceType.display_order
  })

  const iSpaceType = ref({
    id: props.spaceType.id,
    title: props.spaceType.title,
    is_active: props.spaceType.is_active.toString(),
    display_order: props.spaceType.display_order
  })

  //  methods

  const revert = () => {
    iSpaceType.value = {...initialData.value}
  }

  const save = () => {
    console.table({...iSpaceType.value})
    try {
      spaceTypesData.updateSpaceType({...iSpaceType.value}).then( response => {
        console.log( response.data )
        console.log( emit )
        emit('reload')
        ElMessage({
          message: 'Space Type updated',
          type: 'success',
          center: true
        })
      })
    } catch (e) {
      console.log(e)
    }

  }


  
</script>