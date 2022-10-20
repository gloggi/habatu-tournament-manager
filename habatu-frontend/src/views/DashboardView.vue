<template>
   <div class="flex h-full">
      <div class="w-1/4">
         <DashboardMenu/>
      </div>
      <div class="w-3/4 pl-2">
         <router-view></router-view>
      </div>
     

   </div>
   
</template>

<script>
import DashboardMenu from '@/components/DashboardMenu.vue'
export default {
  components: { DashboardMenu },

}
</script>

<style>

</style>