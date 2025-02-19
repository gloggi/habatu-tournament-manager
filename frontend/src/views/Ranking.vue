<script setup lang="ts">
import { useApi } from "@/api";
import Card from "@/components/Card.vue";
import Container from "@/components/Container.vue";
import RankingTable from "@/components/RankingTable.vue";
import { CategoryRankings } from "@/types";

const { fetchData: getRanking, data: ranking } =
  useApi<CategoryRankings>("tournament/ranking");

getRanking(undefined, true);
</script>

<template>
  <Container>
    <div class="flex flex-col space-y-5">
      <template v-for="category in ranking" :key="category.categoryName">
        <template v-if="category.groups.length > 1">
          <Card
            v-for="group in category.groups"
            :key="group.groupName"
            class="w-full"
            :title="`${category.categoryName} - Gruppe ${group.groupName}`"
          >
            <RankingTable :teams="group.ranking" />
          </Card>
        </template>
        <template v-else>
          <Card class="w-full" :title="category.categoryName">
            <RankingTable :teams="category.groups[0].ranking" />
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
