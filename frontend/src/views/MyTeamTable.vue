<script setup lang="ts">
import TournamentTable from "@/components/TournamentTable.vue";
import { TableType } from "@/types";
</script>
<template>
  <TournamentTable :table-type="TableType.Team" />
</template>
