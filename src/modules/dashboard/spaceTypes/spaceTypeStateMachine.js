import { createMachine, interpret } from 'xstate'
import { useActor } from "@xstate/vue";

//  1. create XState machine
const machine = createMachine({
  /** @xstate-layout N4IgpgJg5mDOIC5QBcD2UoBswDoCWAdgIYDGyeAbmAMQAqA8gOKMAyAogNoAMAuoqAAdUsPOVQF+IAB6IAjAFYAzDgCcatQHYVAFnlcATIo0AOADQgAnnI3acXY4seyDDm0YC+782gzYcpcio6JlZOXkkhETEJJGk5JVV1FS1dAyMzSzlDHHkHJxUjDQA2LkUVT290LFwAylxYMGwyAGEAV1g0AFswACdqAGU2dmbaAH1mgFV+hgBZNgAlbj5YyNE8cUkZBG0i+RxdRVlZGxMuLl1zKwR9Lll9pI19fR3b2QqQH2r-MjqcEnaur1+o0wGRINQJgA5QbDMaTab0OaLcIrYRrDaxLY7PYHI4nYxnC6ZBAKLiJdTGWSOFTGDRvd4EVAQOCST7YCJo6KbRAAWiKl152SSwuFinebNwhFqVA5UXWMVAWP0ApJ+hUOCKms18iOKnkau04qqfmlYFl6IVcWu8lst3kRRuJz18j1KqKym0wq4mrUintRt8NR+VBwDSayDaHVQ3R65q5mMQigMdgUDq4TvkLvkKoUGjseUOh3kGnt9gDX1NfwB0aBILBEDj8u5CCKDhwim0RiMKn0smM2mKOZdOQLVNzpeMnk8QA */
  predictableActionArguments: true,
  id: 'spaceTypeMachine',
  type: 'parallel',
  states: {
    spaceTypeSelector: {
      initial: 'spaceTypeList',
      states: {
        'spaceTypeList': {
          on: { 
            EDIT_SPACE_TYPE: 'editSpaceType',
            ADD_SPACE_TYPE: 'addSpaceType'
          }
        },
        'addSpaceType': {
          on: { 
            SPACE_TYPE_LIST: 'spaceTypeList'
          }
        },
        'editSpaceType': {
          on: { 
            SPACE_TYPE_LIST: 'spaceTypeList'
          }
        },
        'deleteSpaceType': {}
      }
    }
    
  }
})

//  2. Create a service 
const service = interpret(machine).start();

//  3. Create a custom service hook for this machine 
//  that consumes the service previously created. 
export const useSpaceTypeMachine = () => {
  return useActor(service);
};
