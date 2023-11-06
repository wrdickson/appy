import { ref } from 'vue'
import { ElNotification } from 'element-plus'
import  router  from '/src/router/index.js'
import { accountStore } from '/src/stores/account.js'

export default () => {
  const v1 = ref(1)
  const v2 = ref(2)

  const directToLogin = () => {
    accountStore().setAccountToGuest()
    router.push( { path: '/Login' } )
  }

  const sayHello = () => {
    ElNotification({
      title: 'Error',
      duration: 0,
      message: 'You do not have permission.  Log in after closing this notifiction.',
      type: 'error',
      onClose: directToLogin
    })
  }

  return { v1, v2, sayHello }

}
