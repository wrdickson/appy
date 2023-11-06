<template>
  <el-row>
    <el-col :span="12">
      <div class="stateViewer">state:<br/>{{state.value}}</div>
      <div>
        Space Types
        <button @click="send('ADD_SPACE_TYPE')">Add</button>
        <el-table @row-click="rowClick" size="small" :data="spaceTypes" style="width: 100%;">
          <el-table-column prop="id" label="Id"/>
          <el-table-column prop="title" label="Title"/>
          <el-table-column prop="is_active" label="Is Active"/>
          <el-table-column prop="display_order" label="Display Order"/>
        </el-table>
      </div>
    </el-col>
    <el-col :span="12">
      <EditSpaceType 
        v-if="state.matches('spaceTypeSelector.editSpaceType')"
        :spaceType = selectedSpaceType
        @reload="loadSpaceTypes"
        />

      <div v-if="state.matches('spaceTypeSelector.addSpaceType')">
        Add Space Type
        <el-button @click="send('SPACE_TYPE_LIST')">Back</el-button>
      </div>
    </el-col>
    </el-row>
</template>

<script setup>
  import spaceTypesData from './data.js'
  import { useSpaceTypeMachine } from './spaceTypeStateMachine.js'
  import{ onMounted, ref } from 'vue'
  import EditSpaceType from './EditSpaceType.vue'

  //  expose state machine functions
  const { state, send } = useSpaceTypeMachine()

  //  refs

  const spaceTypes = ref([])
  const selectedSpaceType = ref(0)

  //  methods

  const rowClick = ( row ) => {
    console.log('row', {...row})
    selectedSpaceType.value = row
    send('EDIT_SPACE_TYPE')
    
  }

  const loadSpaceTypes = () => {
    spaceTypesData.getSpaceTypes().then( response => {
      spaceTypes.value = response.data
    })
  }

  //  lifecycle hooks
  onMounted( () => {
    loadSpaceTypes()
  })


</script>

<style scoped>
.stateViewer {
  font-size: 12px;
  padding: 5px;
  background-color: #011801;
  border-radius: 5px;
}
</style>