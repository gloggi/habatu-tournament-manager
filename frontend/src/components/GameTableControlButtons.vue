<script setup lang="ts">
import { useTournamentTableStore } from "@/stores/tournamentTable";
import { Button } from "@/components/ui/button";
import { ListRestart } from "lucide-vue-next";
import {
  Tooltip,
  TooltipContent,
  TooltipProvider,
  TooltipTrigger,
} from "@/components/ui/tooltip";
import {
  NumberField,
  NumberFieldDecrement,
  NumberFieldIncrement,
  NumberFieldInput,
  NumberFieldContent,
} from "@/components/ui/number-field";
import { Label } from "@/components/ui/label";
import { Megaphone, CheckCircle } from "lucide-vue-next";
const { toggleRefereeView, togglePlayedView } = useTournamentTableStore();

import { storeToRefs } from "pinia";
import { useOptionsStore } from "@/stores/options";
import { computed } from "vue";
const optionsStore = useOptionsStore();
const { localOptions } = storeToRefs(optionsStore);

const refreshRate = computed({
  get: () => localOptions.value.gameTableRefreshRate,
  set: (value: number) => optionsStore.setLocalGameTableRefreshRate(value),
});
</script>
<template>
  <div class="flex">
    <NumberField
      id="refresh-rate"
      :default-value="5"
      :step="1"
      :min="0"
      :format-options="{
        style: 'unit',
        unit: 'second',
        unitDisplay: 'narrow',
      }"
      v-model="refreshRate"
    >
      <div class="flex items-center space-x-2 mr-4">
        <TooltipProvider>
          <Tooltip>
            <TooltipTrigger as-child>
              <Label for="refresh-rate"><ListRestart /></Label>
            </TooltipTrigger>
            <TooltipContent>
              <p>
                Da chasch ihstelle wie oft d Tabelle sött aktualisiert werde.
              </p>
            </TooltipContent>
          </Tooltip>
        </TooltipProvider>

        <NumberFieldContent>
          <NumberFieldDecrement />
          <NumberFieldInput class="w-20 h-10" />
          <NumberFieldIncrement />
        </NumberFieldContent>
      </div>
    </NumberField>

    <TooltipProvider>
      <Tooltip>
        <TooltipTrigger as-child>
          <Button variant="ghost" @click="toggleRefereeView"
            ><Megaphone
          /></Button>
        </TooltipTrigger>
        <TooltipContent>
          <p>
            Wenn da druff drucksch erschiened all Spiel ohni en Schri in grau.
          </p>
        </TooltipContent>
      </Tooltip>
    </TooltipProvider>
    <TooltipProvider>
      <Tooltip>
        <TooltipTrigger as-child>
          <Button variant="ghost" @click="togglePlayedView">
            <CheckCircle
          /></Button>
        </TooltipTrigger>
        <TooltipContent>
          <p>
            Wenn da druff drucksch erschiened all Spiel mit en Schri in grün.
          </p>
        </TooltipContent>
      </Tooltip>
    </TooltipProvider>
  </div>
</template>
