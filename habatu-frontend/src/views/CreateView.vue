<template>
    <div class="p-3 h-screen shadow-lg">
        <router-link v-if="backRoute" :to="{name: backRoute}"><BasicButton class="absolute w-1/12 left-3 bottom-3 rounded-tl-none rounded-br-none">back</BasicButton></router-link>
        <router-link v-if="nextRoute" :to="{name: nextRoute}"><BasicButton @click="$router.push({to: nextRoute })" class="absolute w-1/12 right-3 bottom-3 rounded-tr-none rounded-bl-none">next</BasicButton></router-link>
  <div class="w-full h-full  rounded-lg border flex flex-row text-left">
    
    <form @submit.prevent="createItem" class="w-full flex flex-col space-y-3 border-r p-5 ">
        <h1 class="text-2xl font-bold pb-3">{{name}}</h1>
        <div class="w-full flex flex-col space-y-3" v-for="(row, i) in form" :key="i" >
            <div class="w-full flex justify-between space-x-2">
                <template v-for="(col, j) in row" :key="j">
                <TextInput v-if="col.component=='TextField'" class="w-full" v-model="item[col.model]" :label="col.label" />
                <SelectField v-if="col.component=='SelectField'" :options="col.options" class="w-full" v-model="item[col.model]" :label="col.label" />
            </template>
            </div>
        </div>
       
        <BasicButton>Create</BasicButton>
    </form>
    <div class="w-full p-3 mb-10">
        <div class="rounded-lg border bg-gray-100 h-full overflow-scroll no-scrollbar">
            <CollapseItem v-for="item in items" :title="item.name" :key="item._id">
                <div class="flex flex-col">
                    <div class="w-full">{{item.name}}</div>
                    <div class="self-end"><button @click="deleteItem(item._id)"><TrashIcon/></button></div>
                </div>
            </CollapseItem>

        </div>
    </div>

  </div>
</div>
</template>

<script>
import BasicButton from '@/components/BasicButton.vue';
import CollapseItem from '@/components/CollapseItem.vue';
import TrashIcon from '../components/icons/TrashIcon.vue';
import TextInput from '@/components/TextInput.vue';
import SelectField from '@/components/SelectField.vue';
export default {
    components: { BasicButton, CollapseItem, TrashIcon, TextInput, SelectField },
    props: ["name", "form", "state", "nextRoute", "backRoute"],
    data(){
        return {
            item: {}
        }
    },
    computed:{
        items(){
            return this.$store.state[this.state][this.state]
        }
    },
    methods:{
        deleteItem(id){
            this.$store.dispatch(`${this.state}/delete`,id)

        },
        createItem(){
            this.$store.dispatch(`${this.state}/create`,this.item)
        }
    }
}
</script>

<style>

</style>