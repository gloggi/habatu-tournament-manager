<template>
  <div class="flex justify-stretch h-screen items-center flex-col">
    <div class="rounded-md w-full md:w-1/3 mb-2 bg-white drop-shadow-lg p-3">
        <h1 class="text-3xl font-medium">{{team.name}}</h1>
    </div>
    <div class="flex flex-col w-full h-full md:w-1/3 space-y-2 rounded-md bg-white drop-shadow-lg p-3">
        <template v-for="time in Object.keys(table)" :key="table[time]._id">
            {{time}}
        <GameField :games="[table[time]]" ></GameField>
    </template>
    </div>
    </div>
</template>

<script>
import GameField from '@/components/GameField.vue'
export default {
    data() {
        return {
            table: undefined
        };
    },
    methods: {
        async getGroupTable() {
            const response = await this.callApi("get", `/tournament/table/${this.team._id}`);
            this.table = response.data;
        }
    },
    computed:{
        team(){
            return this.userTeam()
        }
    },
    created() {
        this.getGroupTable();
    },
    components: { GameField }
}
</script>

<style>

</style>