<template>
  <div class="flex justify-center space-x-1 p-2 border font-normal text-center 2xl:text-md 2xl:p-1 rounded-md w-full cursor-pointer" @click="()=>{openGameModal(game)}" draggable="true" @dragstart="evt=>dragStartHandler(evt, game._id)" v-for="(game, i) in games" :style="`background: ${game.category.color};`" :key="i" >
       <div class="w-1/2 text-right">{{game.teamA.name}}</div> <div class="w-1/6">{{game.pointsTeamA}} : {{game.pointsTeamB}}</div> <div class="w-1/2 text-left">{{game.teamB.name}}</div>
      </div>
      <div v-if="games.length==0" class="p-2">
        &nbsp;
      </div>
</template>

<script>
export default {
    props: ["games"],
    methods:{
        dragStartHandler(evt, gameId){
      evt.dataTransfer.setData("gameId", gameId)

    },
    openGameModal(game){
      console.log("open")
      this.$store.commit("gameModal/openModal", game)
    }
    }

}
</script>

<style>

</style>