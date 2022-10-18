<template>
    <router-link :to="to">
      <div class="h-full text-gray-700 drop-shadow-lg aspect-square rounded-md bg-white border p-3 flex flex-col justify-end">
          <div class="h-2/3">
              <slot></slot>
          </div>
          <div class="h-1/3 flex flex-col justify-center text-center align-baseline font-light text-xl md:text-3xl">
            <p>{{name}}</p>
          </div>
      </div>
    </router-link>
   
  </template>
  
  <script>
  export default {
     props: ["name", "to"]
  
  }
  </script>
  
  <style>
  
  </style>