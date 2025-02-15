<!-- UserComponent.vue -->
<template>
  <div>
    <Button @click="createHall">Create Hall</Button>
    <div v-if="loading">Loading...</div>
    <div v-if="error">{{ error }}</div>
    <ul v-if="dataList">
      <li v-for="hall in dataList" :key="hall.id">
        {{ hall.id }} {{ hall.name }}
      </li>
    </ul>
  </div>
</template>

<script setup lang="ts">
import { onMounted } from "vue";
import { useApi } from "../api";
import { IHall } from "@/types/iHall";
import { Button } from "@/components/ui/button";
import { useToast } from "./ui/toast";

const { dataList, loading, error, fetchData, createData } =
  useApi<IHall>("halls");

onMounted(() => {
  fetchData();
});
const { toast } = useToast();

const createHall = async () => {
  await createData({ name: "New Hall" });
  toast({
    title: "Hall Created",
    description: "Hall has been created successfully",
  });
  fetchData();
};
</script>
