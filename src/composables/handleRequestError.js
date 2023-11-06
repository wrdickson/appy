import { ElNotification } from 'element-plus'
  export function useHandleRequestError(response){
    console.log('go', response.request.status)
    if( response ) {
      if(response.request) {
        if(response.request.status) {
          switch (response.request.status) {
            case 400:  //  bad request
              ElNotification({
                title: 'Error 400 - Bad Request',
                duration: 4500,
                message: 'The request was malformed.  Request was not processed',
                type: 'error'
              })
            break
            case 401:  //  unauthorized
              ElNotification({
                title: 'Error 401 - Unauthorized',
                duration: 4500,
                message: 'Authorization failed.  Request was not processed.',
                type: 'error',
                //onClose: directToLogin
              })
            break
            case 403:  //  forbidden
              ElNotification({
                title: 'Error 403 - Forbidden',
                duration: 4500,
                message: 'You are not allowed to perform this task.  Request was not processed',
                type: 'error',
                //onClose: directToLogin
              })
            break
            case 404:  //  not found
              console.log('404')
              ElNotification({
                title: 'Error 404 - Not found',
                duration: 4500,
                message: 'The location of this this request was not found',
                type: 'error'
              })
            break
            case 500:  //  server error
              console.log('500')
              ElNotification({
                title: 'Server Error',
                duration: 4500,
                message: 'There was an error on the server.  Request was not processed',
                type: 'error'
              })
            break
            default:
              ElNotification({
                title: 'Error',
                duration: 4500,
                message: 'There was an error.  Request was not processed',
                type: 'error'
              })
          }
        } else {
          ElNotification({
            title: 'Error',
            duration: 4500,
            message: 'There was an error.  Request was not processed',
            type: 'error'
          })
        }
      } else {
        ElNotification({
          title: 'Error',
          duration: 0,
          message: 'There was an error 1.  Request was not processed',
          type: 'error'
        })
      }
    } else {
      ElNotification({
        title: 'Error',
        duration: 0,
        message: 'There was an error 2.  Request was not processed',
        type: 'error'
      })
    }
  }
  

