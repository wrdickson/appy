<template>
  <el-container>
    <el-aside class="dashboard-aside" width="200px">
      <div class="menu-header">Dashboard</div>
      <el-menu :router="true">
        <el-menu-item index="/dashboard/space-types">Space Types</el-menu-item>
        <el-menu-item index="/dashboard/accounts">Accounts</el-menu-item>
      </el-menu>
    </el-aside>
    <el-main>
      <RouterView/>
    </el-main>
  </el-container>
</template>

<script setup>
  import dashboardData from './data.js'
  import { onMounted } from 'vue'

  //  refs

  //  computed

  //  methods

  //  onMounted
  onMounted( () => {
    console.log('DashboardView onMounted()')
  })

</script>

<style scoped>

.el-menu-item {
  height: 34px;
}

.menu-header {
  margin: 0px;
  padding-bottom: 5px;
  border-bottom: 1px solid #444444;
}

.dashboard-aside {
  background-color: #0f063a;
  border-radius: 4px;
 
}
.menu-header {
  padding-left: 21px;
  margin-top: 20px;
  margin-bottom: 12px;
}
</style>