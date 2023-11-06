const dashboardRoutes = 
  [
    {
      path: '/dashboard',
      component: () => import('./Dashboard.vue'),
      children: [
        {
          path: '/dashboard/space-types',
          component: () => import('./spaceTypes/SpaceTypes.vue') 
        },
        {
          path: '/dashboard/accounts',
          component: () => import('./accounts/Accounts.vue')
        }
      ]
    }
  ]
export default dashboardRoutes