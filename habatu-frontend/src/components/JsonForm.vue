<template>
    <div class="w-full flex flex-col space-y-3" v-for="(row, i) in form" :key="i">
        <div class="w-full flex justify-between space-x-2">
            <template v-for="(col, j) in row" :key="j">
                <TextInput v-if="col.component=='TextField'" class="w-full" v-model="item[col.model]"
                    :label="col.label" :type="col.type" />
                <SelectField v-if="col.component=='SelectField'" :options="col.options" class="w-full"
                    v-model="item[col.model]" :label="col.label" />
            </template>
        </div>
    </div>
</template>

<script>
import TextInput from '@/components/TextInput.vue';
import SelectField from '@/components/SelectField.vue';
export default {
    components: { TextInput, SelectField },
    props:["form", "values"],
    emits:["changeForm"],
    data(){
        return  {
            item: {}
        }
    },
    watch:{
        item:{ handler(newVal){
            this.$emit("changeForm", newVal)
        }, deep: true}

    },
    created(){
        this.values? this.item=this.values:"";
    }

}
</script>

<style>

</style>