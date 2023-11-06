import axios from 'axios'

const optionsData = {

  getAutoloadOptions: () => {
    const request = axios({
      method: 'get',
      url: 'api/autoload-options'
    })
    return request
  }
}

export default optionsData