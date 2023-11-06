import { createMachine, interpret } from "xstate";
import { useActor } from "@xstate/vue";

//  1. create machine
const saleStateMachine = createMachine({
  predictableActionArguments: true,
  id: 'SaleMachine',
  initial: 'inactive',
  states: {
    inactive: {
      on: { CUSTOMER_SELECTED: 'customerSelected' }
    },
    customerSelected: {
      on: { CUSTOMER_UNSELECT: 'inactive' },
      initial: 'selectSaleGroup',
      states: {
        selectSaleGroup: {
          on: {
            PRODUCT_GROUP_SELECTED: 'productGroupSelected'
          }
        },
        productGroupSelected: {
          initial: 'selectProductSubgroup',
          on: {
            UNSELECT_SALE_GROUP: 'selectSaleGroup'
          },
          states: {
            selectProductSubgroup: {
              on: {
                PRODUCT_SUBGROUP_SELECTED: 'productSubgroupSelected'
              }
            },
            productSubgroupSelected: {
              on: {
                PRODUCT_SUBGROUP_UNSELECT: 'selectProductSubgroup'
              },
              initial: 'selectProduct',
              states: {
                selectProduct: {
                  on: {
                    PRODUCT_SELECTED: 'productSelected'
                  }
                },
                productSelected: {
                  on: {
                    PRODUCT_UNSELECT: 'selectProduct'
                  }
                }
              }
            }
          }
        }
      }
    }
  }
});

//  2. Create service 
const service = interpret(saleStateMachine).start();

//  3. Export actor using service
export const useSaleStateMachine = () => {
  return useActor(service);
};