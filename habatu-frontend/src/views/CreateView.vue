<template>
    <div class="p-3 h-screen">
        <router-link v-if="backRoute" :to="{name: backRoute}"><BasicButton class="absolute w-1/12 left-5 bottom-1 rounded-tl-none rounded-br-none">back</BasicButton></router-link>
        <router-link v-if="nextRoute" :to="{name: nextRoute}"><BasicButton @click="$router.push({to: nextRoute })" class="absolute w-1/12 right-5 bottom-1 rounded-tr-none rounded-bl-none">next</BasicButton></router-link>
  <div class="w-full h-full shadow-lg bg-white  rounded-md border flex flex-row text-left">
    
    <form @submit.prevent="createItem" :key="formKey" class="w-full flex flex-col space-y-3 border-r p-5 ">
        <h1 class="text-2xl font-bold pb-3">{{name}}</h1>
        <JsonForm @changeForm="handleMainFormChange" :form="form" />
       
        <BasicButton>Create</BasicButton>
    </form>
    <div class="w-full p-3 mb-10">
        <div class="rounded-lg border bg-gray-100 h-full overflow-scroll no-scrollbar">
            <CollapseItem v-for="item in items" :title="item.name" :key="item._id">
                <div class="flex flex-col">
                    <div class="w-full"><JsonForm @changeForm="handleSideFormChange" :form="form" :values="item" /></div>
                    <div class="self-end flex justify-end space-x-2">
                        <div class="mt-2"><button @click="updateItem(item._id)"><RefreshIcon/></button></div>
                        <div class="mt-2"><button @click="deleteItem(item._id)"><TrashIcon/></button></div>

                    </div>
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
import JsonForm from '@/components/JsonForm.vue';
import RefreshIcon from '@/components/icons/RefreshIcon.vue';
export default {
    components: { BasicButton, CollapseItem, TrashIcon, JsonForm, RefreshIcon },
    props: ["name", "form", "state", "nextRoute", "backRoute"],
    data(){
        return {
            item: {},
            formKey: 0,
            toUpdateItems: {}
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
            this.formKey++
        },
        updateItem(itemId){
            if(this.toUpdateItems[itemId]){
            this.$store.dispatch(`${this.state}/update`,this.toUpdateItems[itemId])
            this.formKey++
            this.toUpdateItems[itemId] = undefined
        }
        },
        handleMainFormChange(item){
            this.item = item
        },
        handleSideFormChange(item){
            this.toUpdateItems[item._id] = item
        }
    }
}
</script>

<style>

</style>