<script setup lang="ts">
import { useApi } from "@/api";
import Card from "@/components/Card.vue";
import Container from "@/components/Container.vue";
import { CategoryRankings } from "@/types";
const { fetchData: getRanking, data: ranking} = useApi<CategoryRankings>("tournament/ranking");


getRanking(undefined, true);



</script>
<template>
    <Container>
    <div class="flex flex-col space-y-5">
        <template v-for="category in ranking" :key="category.categoryName">
            <template v-if="category.groups.length > 1">
                <Card v-for="group in category.groups" :key="group.groupName" class="w-full" :title="`${category.categoryName} - Gruppe ${group.groupName}`">
                    <div class="flex font-semibold space-x-2">
                        <p class="points-item">P</p>
                        <p class="w-1/6">Teamname</p>
                        <p class="w-1/6">Abteilung</p>
                        <p class="points-item">SP.</p>
                        <p class="points-item">S</p>
                        <p class="points-item">U</p>
                        <p class="points-item">N</p>
                        <p class="points-item">T+</p>
                        <p class="points-item">T-</p>
                        <p class="points-item">T+/-</p>
                        <p class="points-item">PKT</p>
                    </div>
                    <div class="flex flex-col" v-for="team in group.ranking" :key="team.team.id">
                        <div class="flex space-x-2">
                            <p class="points-item font-semibold">{{ team.rank }}.</p>
                            <p class="w-1/6">{{ team.team.name }}</p>
                            <p class="w-1/6">{{ team.team.section?.name }}</p>
                            <p class="points-item">{{ team.matchesPlayed }}</p>
                            <p class="points-item">{{ team.wins }}</p>
                            <p class="points-item">{{ team.draws }}</p>
                            <p class="points-item">{{ team.losses }}</p>
                            <p class="points-item">{{ team.goalsScored }}</p>
                            <p class="points-item">{{ team.goalsConceded }}</p>
                            <p class="points-item">{{ team.goalsDifference }}</p>
                            <p class="points-item">{{ team.points }}</p>
                        </div>
                    </div>
                </Card>
            </template>
            <template v-else>
                <Card class="w-full" :title="category.categoryName">
                    <div class="flex font-semibold space-x-2">
                        <p class="points-item">P</p>
                        <p class="w-1/6">Teamname</p>
                        <p class="w-1/6">Abteilung</p>
                        <p class="points-item">SP.</p>
                        <p class="points-item">S</p>
                        <p class="points-item">U</p>
                        <p class="points-item">N</p>
                        <p class="points-item">T+</p>
                        <p class="points-item">T-</p>
                        <p class="points-item">T+/-</p>
                        <p class="points-item">PKT</p>
                    </div>
                    <div class="flex flex-col" v-for="team in category.groups[0].ranking" :key="team.team.id">
                        <div class="flex space-x-2">
                            <p class="points-item font-semibold">{{ team.rank }}.</p>
                            <p class="w-1/6">{{ team.team.name }}</p>
                            <p class="w-1/6">{{ team.team.section?.name }}</p>
                            <p class="points-item">{{ team.matchesPlayed }}</p>
                            <p class="points-item">{{ team.wins }}</p>
                            <p class="points-item">{{ team.draws }}</p>
                            <p class="points-item">{{ team.losses }}</p>
                            <p class="points-item">{{ team.goalsScored }}</p>
                            <p class="points-item">{{ team.goalsConceded }}</p>
                            <p class="points-item">{{ team.goalsDifference }}</p>
                            <p class="points-item">{{ team.points }}</p>
                        </div>
                    </div>
                </Card>
            </template>
        </template>
    </div>
</Container>

</template>

<style>
.points-item {
    width: 6.6%;
    text-align: center;
}
</style>