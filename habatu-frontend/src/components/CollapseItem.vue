<template>
  <div class="w-full flex flex-col">
      <button @click="show=!show" class="w-full rounded top-lg p-3 text-left bg-gray-200 text-lg cursor-pointer">
        {{title}}
      </button>

    <Transition name="collapse">
      <div class="w-full p-3 px-5 bg-white overflow-hidden" v-if="show">
       <slot></slot>
      </div>
    </Transition>
  </div>
</template>

<script>
export default {
  props: ["title"],
  data() {
    return {
      show: false
    }
  }

}
</script>

<style>
.collapse-enter-active {
  animation: collapse reverse 300ms cubic-bezier(0, 0, 1, 1);
}

.collapse-leave-active {
  animation: collapse 300ms cubic-bezier(0, 0, 1, 1);
}

@keyframes collapse {
  from {
    max-height: 200px;
  }

  to {
    max-height: 0px;
    padding-top: 0;
    padding-bottom: 0;

  }
}
</style>