<template>
  <GenericModal v-if="isOpen" :close="close" :title="`${game.teamA.name} vs ${game.teamB.name}`">
    {{game}}
  </GenericModal>
</template>

<script>
import GenericModal from './GenericModal.vue';
export default {
    components: { GenericModal },
    methods:{
        close(){
            this.$store.commit("gameModal/closeModal")
        }
    },
    computed:{
        isOpen(){
            return this.$store.state.gameModal.open
        },
        game(){
            return this.$store.state.gameModal.game
        }
    }
}
</script>

<style>

</style>