<template>
    <div class="">
  <div class="w-full rounded-md bg-white border flex justify-between p-3 items-center">
    <div class="flex items-center space-x-3">
        <img src="@/assets/rotating_ball_a.png" class="h-16 w-16" />
        <h1 class="md:text-3xl text-2xl font-black text-gray-800">HaBaTu 2022</h1>
    </div>
    <div class="md:text-6xl text-2xl font-black text-gray-800">{{time}}</div>
  </div>
</div>
</template>

<script>
import {format} from "date-fns"
export default {
    data(){
        return {
            time: undefined
        }
    },
    methods:{
        setTime(){
            this.time =  format(new Date(), "HH:mm:ss")

        }
    },
    created(){
        setInterval(this.setTime,1000)
    }

}
</script>

<style>

</style>