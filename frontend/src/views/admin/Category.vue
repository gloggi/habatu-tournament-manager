<script setup lang="ts">
import GenericEditComponent from '@/components/EditEntity.vue';
import InputField from '@/components/InputField.vue';
</script>

<template>
  <GenericEditComponent resourceName="categories">
    <template #default="{ item }">
      <InputField v-model="item.name" label="Name" />
      <InputField v-model="item.color" label="Farbe" type="color" />
    </template>
  </GenericEditComponent>
</template>