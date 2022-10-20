<template>
    <router-link :to="{name: to}">
  <div class="flex items-center space-x-4 rounded-md p-2 px-4 m-1 bg-gray-900 hover:bg-gray-800 text-white">
       <div><slot></slot></div>
       <div>{{title}}</div>
    </div>
</router-link>
</template>

<script>
export default {
    props:["title", "to"]

}
</script>

<style>

</style>