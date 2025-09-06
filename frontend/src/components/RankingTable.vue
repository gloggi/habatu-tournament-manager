<script setup lang="ts">
import type { RankedTeam } from "@/types";

defineProps<{
  teams: RankedTeam[];
}>();
</script>

<template>
  <div>
    <div class="flex font-semibold space-x-2">
      <p class="points-item">🏅</p>
      <p class="w-1/3 md:w-1/6">Teamname</p>
      <p class="w-0 md:w-1/6 hidden md:block">Abteilung</p>
      <p class="points-item">SP.</p>
      <p class="points-item">S</p>
      <p class="points-item">U</p>
      <p class="points-item">N</p>
      <p class="points-item hidden md:block">T+</p>
      <p class="points-item hidden md:block">T-</p>
      <p class="points-item hidden md:block">T+/-</p>
      <p class="points-item">PKT</p>
    </div>

    <div class="flex flex-col" v-for="team in teams" :key="team.team.id">
      <div class="flex space-x-2">
        <p class="points-item font-semibold">{{ team.rank }}.</p>
        <p class="w-1/3 md:w-1/6">{{ team.team.name }}</p>
        <p class="w-0 md:w-1/6 hidden md:block">
          {{ team.team.section?.name }}
        </p>
        <p class="points-item">{{ team.matchesPlayed }}</p>
        <p class="points-item">{{ team.wins }}</p>
        <p class="points-item">{{ team.draws }}</p>
        <p class="points-item">{{ team.losses }}</p>
        <p class="points-item hidden md:block">{{ team.goalsScored }}</p>
        <p class="points-item hidden md:block">{{ team.goalsConceded }}</p>
        <p class="points-item hidden md:block">{{ team.goalsDifference }}</p>
        <p class="points-item">{{ team.points }}</p>
      </div>
    </div>
  </div>
</template>
