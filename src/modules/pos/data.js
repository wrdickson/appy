import axios from 'axios'
import { authStore } from './../../modules/auth/store.js'

const posData = {

  getAllCustomers: () => {
    const promise = axios({
      method: 'get',
      headers: {
        'jwt': authStore().token
      },
      url: 'api/customers'
    })
    return promise
  },

  getPosData: () => {
    const promise = axios({
      method: 'get',
      headers: {
        'jwt': authStore().token
      },
      url: 'api/pos-data'
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
  },

  searchProductsByCode: ( args ) => {
    const request = axios({
      method: 'post',
      data: args,
      headers: { 
        'jwt': authStore().token
      },
      url: 'api/product-search-code/'
    })
    return request
  }
}

export default posData