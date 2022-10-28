<template>
    <div :key="labelKey">
    <label class="mb-1 block text-sm font-bold text-gray-700" for="username">
			{{ label }}
		</label>
<div class="relative inline-block w-10 mr-2 align-middle select-none transition duration-200 ease-in">
    <input type="checkbox" :ref="`checkbox-${label}`" @change="handleChange" :name="`toggle-${label}`" class="toggle-checkbox absolute block w-6 h-6 rounded-full bg-white border-4 appearance-none cursor-pointer"/>
    <label for="toggle" class="toggle-label block overflow-hidden h-6 rounded-full bg-gray-300 cursor-pointer"></label>
</div>
</div>

</template>

<script>
export default {
    props: ["modelValue", "label"],
    data(){
        return {
            labelKey: 0

        }
    },
    watch: {
    },
    methods: {
        handleChange(event) {
            event.target.checked = true
            console.log(event.target.value)
            this.$refs[`checkbox-${this.label}`].checked = !this.modelValue
            this.$emit('update:modelValue', !this.modelValue)
        }
    },
    mounted() {
        this.$refs[`checkbox-${this.label}`].checked = this.modelValue
        console.log("box",this.modelValue)
       
    }

}
</script>

<style>
.toggle-checkbox:checked {
  @apply: right-0 border-green-400;
  right: 0;
  border-color: #000000;
}
.toggle-checkbox:checked + .toggle-label {
  @apply: bg-green-400;
  background-color: #000000;
}

</style>