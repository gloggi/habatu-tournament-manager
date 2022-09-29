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
#app {
  font-family: Avenir, Helvetica, Arial, sans-serif;
  -webkit-font-smoothing: antialiased;
  -moz-osx-font-smoothing: grayscale;
  text-align: center;
  color: #2c3e50;
}

nav {
  padding: 30px;
}

nav a {
  font-weight: bold;
  color: #2c3e50;
}

nav a.router-link-exact-active {
  color: #42b983;
}
</style>
