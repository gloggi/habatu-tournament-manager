<script setup lang="ts">
import {
  HouseIcon,
  TrophyIcon,
  MapIcon,
  UsersIcon,
  Layers3Icon,
} from "lucide-vue-next";

import {
  Stepper,
  StepperIndicator,
  StepperItem,
  StepperSeparator,
  StepperTitle,
  StepperTrigger,
} from "@/components/ui/stepper";

const steps = [
  {
    step: 0,
    title: "Hallen erfassen",
    description: "Füge alle Hallen hinzu",
    icon: HouseIcon,
  },
  {
    step: 1,
    title: "Kategorien erfassen",
    description: "Füge alle Kategorien hinzu",
    icon: Layers3Icon,
  },
  {
    step: 2,
    title: "Abteilungen erfassen",
    description: "Füge alle Abteilungen hinzu",
    icon: MapIcon,
  },
  {
    step: 3,
    title: "Teams erfassen",
    description: "Füge alle Teams hinzu",
    icon: UsersIcon,
  },
  {
    step: 4,
    title: "Tournier generieren",
    description: "Generiere ein Tournier",
    icon: TrophyIcon,
  },
];

const currentStep = defineModel("currentStep", {
  type: Number,
  default: 1,
});
</script>

<template>
  <Stepper v-model="currentStep" :linear="false">
    <StepperItem
      v-for="item in steps"
      :key="item.step"
      class="basis-1/4"
      :step="item.step"
    >
      <StepperTrigger>
        <StepperIndicator>
          <component :is="item.icon" class="w-4 h-4" />
        </StepperIndicator>
        <div class="flex flex-col">
          <StepperTitle>
            {{ item.title }}
          </StepperTitle>
        </div>
      </StepperTrigger>
      <StepperSeparator
        v-if="item.step !== steps[steps.length - 1].step"
        class="w-full h-px"
      />
    </StepperItem>
  </Stepper>
</template>
