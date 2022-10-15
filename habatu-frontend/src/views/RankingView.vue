<template>

    <div v-if="rank" class="flex w-full space-x-2 p-3">
    <div class="w-full rounded-md border flex flex-col p-3"  v-for="(category, i) in Object.keys(rank)" :key="i">
    <div class="flex font-bold">
        <div class="w-1/2">{{category}}</div>
        <div class="w-1/4">Abteilung</div>
        <div class="w-1/4 text-end">P:GG:GK</div>
    </div>
    <div class="flex justify-between" v-for="(team, k) in rank[category]" :key="team._id">
        <div class="w-1/2">{{k+1}}. {{team.name}}</div>
        <div class="w-1/4" v-if="team.section">{{team.section.name}}</div>
        <div class="w-1/4 text-end">{{team.tournamentPoints}}:{{team.pointsPro}}:{{team.pointsCon}}</div>
    </div>
    </div>
</div>
</template>

<script>
export default {
    data(){
        return {
            rank: undefined

        }
    },
    methods:{
        async getRanking(){
            const response = await this.callApi("get", "/tournament/ranking")
            this.rank = response.data
            console.log(this.rank["Gemischt"][0].section.name)

        }

    },
    computed:{
    },
    created(){
        this.getRanking()
    }

}
</script>

<style>

</style>