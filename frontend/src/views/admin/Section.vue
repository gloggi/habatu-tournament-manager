<script setup lang="ts">
import GenericEditComponent from "@/components/EditEntity.vue";
import InputField from "@/components/InputField.vue";
</script>

<template>
  <GenericEditComponent resourceName="sections">
    <template #default="{ item }">
      <InputField v-model="item.name" label="Name" />
    </template>
  </GenericEditComponent>
</template>
