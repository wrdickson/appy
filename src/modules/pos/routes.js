const posRoutes = 
  [
    {
      path: '/folio-sale',
      component: () => import('./folioSale/FolioSale.vue')
    },
    { 
      path: '/sales',
      component: () => import('./sale/Sales.vue')
    },
    {
      path: '/postSale',
      component: () => import('./PostSale.vue') 
    }
  ]
export default posRoutes