import axios from 'axios'
import { authStore } from './../../../modules/auth/store.js'

const spaceTypesData = {
  getSpaceTypes : () => {
    const promise = axios({
      method: 'get',
      url: 'api/spacetypes'
    })
    return promise
  },
  updateSpaceType: ( spaceType ) => {
    const promise = axios({
      method: 'post',
      headers: {
        Jwt: authStore().token
      },
      data: spaceType,
      url: 'api/spacetypes/' + spaceType.id
    })
    return promise
  }
}

export default spaceTypesData