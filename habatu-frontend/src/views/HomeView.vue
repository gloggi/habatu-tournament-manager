<template>
  <div v-if="games" >
    
    <div class="flex flex-row space-x-4 justify-even items-center space-y-4" v-for="timeslot in Object.keys(games.table)" :key="timeslot">
     <div class="p-3 w-1/2"> {{format(new Date(timeslot),"HH:mm")}}</div>
     <div v-for="(hall, i) in Object.keys(games.table[timeslot])" :style="`background: ${games.table[timeslot][hall][0].category.color};`" :key="i" :class="`p-2 border font-bold rounded-md w-full ${games.table[timeslot][hall][0].teamA.name===games.table[timeslot][hall][0].teamB.name?'text-red':''}`"> {{games.table[timeslot][hall][0].teamA.name}} vs {{games.table[timeslot][hall][0].teamB.name}}</div>
     <div v-for="i in games.halls.length-Object.keys(games.table[timeslot]).length" :key="i"></div>
    </div>
    
  </div>
</template>

<script>
// @ is an alias to /src

/*



*/
import {format} from "date-fns"

export default {
  name: 'HomeView',
  data(){
    return {
      games: undefined

    }
  },
  components: {
  },
  methods:{
    async getGames(){
      console.log(this)
      const games = await this.callApi("get","/games/preview")
      this.games = games.data;

    },
    format(date, form){
      return format(date, form)
    }
  },
  async created(){
    this.getGames()
  }
}
</script>
