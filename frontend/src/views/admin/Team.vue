<script setup lang="ts">
import ComboBox from '@/components/ComboBox.vue';
import GenericEditComponent from '@/components/EditEntity.vue';
import InputField from '@/components/InputField.vue';
</script>

<template>
  <GenericEditComponent resourceName="teams">
    <template #default="{ item }">
      <InputField v-model="item.name" label="Name" />
      <ComboBox v-model="item.sectionId" label="Section" entity="sections" optionsEntity="sections" />
      <ComboBox v-model="item.categoryId" label="Category" entity="categories" optionsEntity="categories" />
    </template>
  </GenericEditComponent>
</template>