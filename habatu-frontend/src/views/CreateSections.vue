<template>
  <CreateView :name="name" :form="form" />
</template>

<script>
import CreateView from './CreateView.vue';
export default {
    components: { CreateView },
    data(){
        return{
            name: "Abteilungen",
            form: [
                [
                    {label: "Abteilung", model: "name", component: "TextField"}, 
                    {label: "Abteilung", model: "name", component: "TextField"}
                ],[
                    {label: "Kategorie", model: "category", component: "SelectField", options:[{_id:0, name: "John Doe"}, {_id:1, name: "Erika Mustermann"}]}
                ]
            ]
        }
    },
    methods:{
        

    }
}
</script>

<style>

</style>