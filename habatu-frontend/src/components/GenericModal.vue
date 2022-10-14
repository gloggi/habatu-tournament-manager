<template>
  <div class="absolute top-36 bottom-36 right-1/4 left-1/4 z-50 p-5 pt-20  bg-white border drop-shadow-lg rounded-md">
    <div class="absolute top-5 r-2 text-2xl font-black">
        {{title}}
    </div>
    <button @click="close" class="absolute border rounded-bl-md rounded-tr-md p-5 top-0 right-0 text-xl"><XIcon/></button>
    <div class="overflow-scroll no-scrollbar h-full w-full">
    <div class="flex flex-col space-y-2">
        <slot></slot>
    </div>
</div>
</div>
</template>

<script>
import XIcon from './icons/XIcon.vue';
export default {
    components: { XIcon },
    props: ["title", "close"]
}
</script>

<style>


</style>