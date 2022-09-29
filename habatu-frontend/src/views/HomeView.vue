<template>
  <div v-if="timeslots" :key="tableKey" class="space-y-4 mt-3 px-2 h-full w-full" >
    <div class="flex flex-row space-x-4 justify-center items-baseline">
      <div class="w-1/2"></div>
      <div v-for="hall in halls" :key="hall._id" class="w-full text-xl">{{hall.name}}</div>
    </div>
    <div :class="`flex flex-row space-x-4 justify-center items-baseline ${i==5?'ring-2 ring-red-400 ring-offset-1 ring-opacity-75 rounded-lg':''}`" v-for="(timeslot, i) in Object.keys(timeslots)" :key="timeslot">
     <div class="w-1/2 self-center">{{timeslot.split("|")[0]}}</div>
     <DragSlot class="w-full self-stretch" @reload="handleReload" :hall="hall.split('|')[1]" :timeslot="timeslot.split('|')[1]" v-for="(hall) in Object.keys(timeslots[timeslot])"  :key="hall.name">
     <GameField :games="timeslots[timeslot][hall]" />
    </DragSlot>
    </div>
  </div>
  <GameModal />
</template>

<script>
// @ is an alias to /src

/*



*/
import {format} from "date-fns"
import DragSlot from "@/components/DragSlot.vue"
import GameField from "@/components/GameField.vue"
import GameModal from "@/components/GameModal.vue"

export default {
  name: 'HomeView',
  data(){
    return {
      timeslots: undefined,
      halls: undefined,
      tableKey: 0

    }
  },
  components: {
    DragSlot,
    GameField,
    GameModal
},
  methods:{
    async getTimeslots(){
      console.log(this)
      const timeslots = await this.callApi("get","/games/preview")
      this.timeslots = timeslots.data
      console.log("reload")
      this.tableKey++
    },
    async getHalls(){
      console.log(this)
      const halls = await this.callApi("get","/halls")
      this.halls = halls.data
    },
    handleReload(){
      this.getTimeslots()
    },
    dragStartHandler(evt, gameId){
      evt.dataTransfer.setData("gameId", gameId)

    },
    format(date, form){
      return format(date, form)
    }
  },
  async created(){
    this.getHalls()
    this.getTimeslots()
    await this.$store.dispatch('games/get');
  }
}
</script>
