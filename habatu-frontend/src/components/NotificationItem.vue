<template>
     <Transition name="list">
    <div v-if="show" class="sticky bottom-0 w-full overflow-x-hidden">
        <div class="flex w-full justify-end p-3">
           
            <div v-if="show" class="flex items-center space-x-3 bg-white bottom-3 w-1/4 left-3 right-3 rounded-lg border p-3 shadow-lg">
               <CheckIcon class="text-green-600" /><div class="font-bold text">{{message}}</div> 
            </div>
        
        </div>
    </div>
</Transition>

</template>

<script>
import CheckIcon from './icons/CheckIcon.vue';
export default {
    components: { CheckIcon },
    data(){
        return {
           
        }
    },
    computed:{
        show(){
            return this.$store.state.notifications.show
        },
        message(){
            return this.$store.state.notifications.message
        }

    }
}
</script>

<style>
.list-enter-active,
.list-leave-active {
  transition: all 0.5s ease;
}
.list-enter-from,
.list-leave-to {
  opacity: 0;
  transform: translateX(30px);
}

</style>