<template>
  <div @click="()=>{openGameModal(game)}" draggable="true" @dragstart="evt=>dragStartHandler(evt, game._id)" v-for="(game, i) in games" :style="`background: ${game.category.color};`" :key="i" class="p-2 border font-bold rounded-md w-full cursor-pointer">
       {{game.teamA.name}} {{game.pointsTeamA}} : {{game.pointsTeamB}} {{game.teamB.name}}
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