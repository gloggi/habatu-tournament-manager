<template>
  <Container>
    <EntityTable
      entity="users"
      name="Users"
      :columnKeys="['nickname', 'boolean(pushSubscriptionsExists)']"
      :columnNames="['Pfadiname', 'Push-Benachrichtigungen aktiviert']"
    />
  </Container>
</template>

<script setup lang="ts">
import Container from "@/components/Container.vue";
import EntityTable from "@/components/EntityTable.vue";
</script>
