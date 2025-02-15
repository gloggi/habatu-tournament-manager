<template>
  <Container>
    <EntityTable
      entity="categories"
      name="Kategorien"
      :columnKeys="['name']"
      :columnNames="['Name']"
    />
  </Container>
</template>

<script setup lang="ts">
import Container from "@/components/Container.vue";
import EntityTable from "@/components/EntityTable.vue";
</script>
