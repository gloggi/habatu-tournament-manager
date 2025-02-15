<template>
  <div class="container pt-5 h-full">
    <div class="flex flex-col h-full p-5" v-if="currentStep < steps.length">
      <CreationStepper v-model:currentStep="currentStep" class="mb-5" />
      <CreateEntity
      class="h-full"
        v-if="steps[currentStep].component === 'CreateEntity'"
        :entity="steps[currentStep].entity"
        :name="steps[currentStep].name"
        :columnNames="steps[currentStep].columnNames"
        :columnKeys="steps[currentStep].columnKeys"
        :form="steps[currentStep].form"
        v-model:currentStep="currentStep"
      />
      <CreateTournament
        v-else
        />
        <div class="flex justify-between pt-5">
    <Button variant="outline" @click="currentStep--">Zurück</Button>
    <Button variant="outline" @click="currentStep++">Weiter</Button>
  </div>

    </div>
  </div>
</template>
<script setup lang="ts">
import CreateEntity from "@/components/CreateEntity.vue";
import CreateTournament from "@/components/CreateTournament.vue";
import CreationStepper from "@/components/CreationStepper.vue";
import { Button } from "@/components/ui/button";
import { ref, watch } from "vue";
import { useRoute, useRouter } from "vue-router";

const route = useRoute();
const router = useRouter();
const entity : string = route.params.entity? route.params.entity.toString() : "halls";

const entityToStep = {
  halls: 0,
  categories: 1,
  sections: 2,
  teams: 3,
  tournaments: 4,
};

var startingstep = entityToStep[entity as keyof typeof entityToStep] || 0;

const currentStep = ref(startingstep);

watch(currentStep, (newStep) => {
  if (newStep >= steps.length) {
    router.push({ name: 'Home' });
    return
  }
  const entity = steps[newStep].entity;
  if (entity) {
    router.push({ name: 'Create', params: { entity } });
  }
});

const steps = [
  {
    entity: "halls",
    component: "CreateEntity",
    name: "Hallen",
    columnKeys: ["name"],
    columnNames: ["Name"],
    form: [
      {
        label: "Hallenname",
        key: "name",
        type: "text",
        required: true,
      },
    ],
  },
  {
    entity: "categories",
    component: "CreateEntity",
    name: "Kategorien",
    columnKeys: ["name"],
    columnNames: ["Name"],
    form: [
      {
        label: "Name",
        key: "name",
        type: "text",
        required: true,
      },
      {
        label: "Farbe",
        key: "color",
        type: "color",
        required: true,
      },
    ],
  },
  {
    entity: "sections",
    component: "CreateEntity",
    name: "Abteilungen",
    columnKeys: ["name"],
    columnNames: ["Name"],
    form: [
      {
        label: "Name",
        key: "name",
        type: "text",
        required: true,
      },
    ],
  },
  {
    entity: "teams",
    component: "CreateEntity",
    name: "Teams",
    columnKeys: ["name", "section.name", "category.name"],
    columnNames: ["Name", "Abteilung", "Kategorie"],
    form: [
      {
        label: "Name",
        key: "name",
        type: "text",
        required: true,
      },
      {
        label: "Abteilung",
        optionsEntity: "sections",
        key: "sectionId",
        type: "select",
        required: true,
      },
      {
        label: "Kategorie",
        optionsEntity: "categories",
        key: "categoryId",
        type: "select",
        required: true,
      },
    ],
  },
  {
    component: "Tournaments",
    entity: "tournaments",
    name: "Tournaments",
    columnNames: [],
    columnKeys: [],
    form: [],

  }
];
</script>
