import { createMachine, interpret } from "xstate";
import { useActor } from "@xstate/vue";

//  1. create machine
const saleStateMachine = createMachine({
  predictableActionArguments: true,
  id: 'FolioSaleMachine',
  initial: 'selectProduct',
  states: {
    selectProduct: {
      on: { PRODUCT_SELECTED: 'productSelected' }
    },
    productSelected: {
      on: { PRODUCT_UNSELECT: 'selectProduct' }
    }
  }
})

//  2. Create service 
const service = interpret(saleStateMachine).start();

//  3. Export actor using service
export const useFolioSaleStateMachine = () => {
  return useActor(service);
};