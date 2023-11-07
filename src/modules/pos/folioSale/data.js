import axios from 'axios'
import { authStore } from './../../../modules/auth/store.js'

const folioSaleData = {

  getPaymentTypes: () => {
    const promise = axios({
      method: 'get',
      headers: {
        'jwt': authStore().token
      },
      url: 'api/payment-types'
    })
    return promise
  },

  getTaxTypes: () => {
    const promise = axios({
      method: 'get',
      headers: {
        'jwt': authStore().token
      },
      url: 'api/tax-types'
    })
    return promise
  },

  postSale: (args) => {
    const request = axios({
      method: 'post',
      data: args,
      headers: { 
        'jwt': authStore().token
      },
      url: 'api/sales/'
    })
    return request
  }

}

export default folioSaleData