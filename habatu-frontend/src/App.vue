<template>
  <router-view/>
</template>
<script>

export default {
  async created(){
    await this.$store.dispatch('games/get');
    await this.$store.dispatch('halls/get');
    await this.$store.dispatch('categories/get');
    await this.$store.dispatch('options/get');
    await this.$store.dispatch('sections/get');
    await this.$store.dispatch('teams/get');
    await this.$store.dispatch('timeslots/get');
  }

}
</script>


<style>
    .no-scrollbar::-webkit-scrollbar {
    display: none;
}

/* Hide scrollbar for IE, Edge and Firefox */
.no-scrollbar {
    -ms-overflow-style: none;  /* IE and Edge */
    scrollbar-width: none;  /* Firefox */
}
</style>
