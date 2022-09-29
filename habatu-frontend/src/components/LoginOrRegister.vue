<template>
  <div class="flex justify-center space-x-2 p-2 w-full">
    <button @click="handleSwitch" :class="`w-1/4 p-1 text-xs  rounded-md ${login?'bg-gray-900 text-white':'border hover:bg-gray-900 hover:text-white'} `">Login</button>
    <button @click="handleSwitch" :class="`w-1/4 p-1 text-xs  rounded-md ${!login?'bg-gray-900 text-white':'border hover:bg-gray-900 hover:text-white'} `">Register</button>
  </div>
</template>

<script>
export default {
    props: ["login"],
    emits: ["switch"],
    data(){
      return {

      }
    },
    methods:{
      handleSwitch(){
        this.$emit("switch", !this.login)
        console.log("switch")
      }
    }

}
</script>

<style>

</style>