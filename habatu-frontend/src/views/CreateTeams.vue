<template>
    <CreateView 
    :nextRoute="nextRoute" 
    :backRoute="backRoute" 
    :name="name" 
    :state="state" 
    :form="form" />
</template>

<script>
import CreateView from './CreateView.vue';
export default {
    components: { CreateView },
    data() {
        return {
            name: "Teams",
            state: "teams",
            nextRoute: "home",
            backRoute: "categories",
        }
    },
    methods: {


    },
    computed:{
        form(){
            return  [
                [
                    { label: "Teamname", model: "name", component: "TextField" }
                ],
                [
                    { label: "Abteilung", model: "section", options: this.$store.state.sections.sections, component: "SelectField" }
                ],
                [
                    { label: "Kategorie", model: "category", options: this.$store.state.categories.categories, component: "SelectField" }
                ]
            ]
        }
    }
}
</script>

<style>

</style>