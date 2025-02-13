<script setup lang="ts">
import GenericEditComponent from '@/components/EditEntity.vue';
import InputField from '@/components/InputField.vue';
import ComboBox from '@/components/ComboBox.vue';
</script>

<template>
  <GenericEditComponent resourceName="users">
    <template #default="{ item }">
      <InputField v-model="item.nickname" label="Pfadiname" />
      <ComboBox v-model="item.teamId" label="Team" optionsEntity="teams" />
      <ComboBox v-model="item.sectionId" label="Abteilung" optionsEntity="sections" />
      <ComboBox v-model="item.role" label="Rolle" optionsEntity="roles" />
      
    </template>
  </GenericEditComponent>
</template>