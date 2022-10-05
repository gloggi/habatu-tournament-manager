<template>
    <div class="w-full relative">
      <label class="block text-gray-700 text-sm font-bold mb-1" for="username">
        {{ label }}
      </label>
      <input
        v-model="textValue"
        @blur="handleBlur"
        class="
          appearance-none
          border
          rounded
          w-full
          py-2
          px-3
          text-gray-700
          leading-tight
          focus:ring-1
          focus:ring-gray-400
          focus:outline-none focus:shadow-outline
        "
        :id="label"
        :type="type"
        :placeholder="label"
      />
        <div id="dropdown" v-if="select.length>0" class="absolute left-0 right-0 p-3 rounded-b-lg bg-gray-100 text-left z-50">
        <button id="dropdown" @click="handleSelect(item.name)" v-for="item in select" class="w-full rounded-lg bg-gray-100 hover:bg-gray-300 p-3 cursor-pointer" :key="item.name">
        {{item.name}}
        </button>
    </div>
    </div>
  </template>
  
  <script>
  export default {
    props: ["label", "type", "modelValue", "options"],
    emits: ["update:modelValue"],
    data(){
        return {
            select : [],
            selectKey: 0,
            textValue: undefined

        }
    },
    methods:{
        handleSelect(name){
          this.$emit('update:modelValue', this.options.find(o=>o.name==name)._id)
          this.textValue=name
            this.select=[]
        },
        handleBlur(evt){
          if(evt.relatedTarget&&evt.relatedTarget.id=="dropdown"){
            return
          }
          this.select=[]
        }
    },
    watch: {
        modelValue(newVal){
          const value = this.options.find(o=>o._id==newVal)
          if(value){
            this.textValue = value.name
          }
            
        },
        textValue(newVal){
          console.log("watch")
          if(this.options.map(o=>o.name).indexOf(newVal)>-1){
            this.$emit('update:modelValue', this.options.find(o=>o.name==newVal)._id)
                this.select = []
                return
            }
            this.$emit('update:modelValue', undefined)
            this.select = this.options.filter(o=>o.name.toLowerCase().includes(newVal.toLowerCase()))
            console.log(this.select[0])
            this.selectKey++

        }
    }
  };
  </script>
  
  <style>
  </style>