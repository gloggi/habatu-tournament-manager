<script setup lang="ts">
import { useRouter } from "vue-router";
import TournamentTable from "@/components/TournamentTable.vue";
import { TableType } from "@/types";

const router = useRouter();

const handleClickOnGame = (gameId: number) => {
  router.push({ name: "Whistle", params: { id: gameId.toString() } });
};
</script>
<template>
  <TournamentTable
    :table-type="TableType.Referee"
    @click-on-game="handleClickOnGame"
  />
</template>
