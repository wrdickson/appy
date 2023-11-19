<template>
  <h1>Home</h1>
  <localeSwitch/>
    <div>{{howdy}}</div>
    <hr/>
    <el-config-provider :locale="locale">
      <el-table mb-1 :data="[]" />
      <el-pagination :total="100" />
    </el-config-provider>
    <hr/>
    <div>{{lStore.someVar}}</div>
</template>

<script setup>
  import { useI18n } from "vue-i18n"
  import localeSwitch from '@/components/localeSwitch.vue'
  import { localeStore } from '@/modules/locale/store.js'
  const lStore = localeStore()
  import { computed } from 'vue'
  const { t } = useI18n()

  const howdy = computed( () => {
    return t('message.hello')
  })

  const locale = computed( () => {
    return lStore.selectedLocale
  })
</script>
