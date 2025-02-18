<template>
  <div
    v-if="specs"
    class="flex flex-col md:flex-row space-y-3 md:space-y-0 md:space-x-3 w-full h-full items-stretch"
  >
    <div class="flex flex-col space-y-3 w-full">
      <Card title="Spielzeiten">
        <div class="flex flex-col space-y-5">
          <InputField
            label="Startzeit"
            type="time"
            default-value="10:00:00"
            v-model="specs.startTime"
          />
          <NumberInputField
            label="Spielzeit (in Minuten)"
            :default-value="10"
            v-model="specs.gameDuration"
          />
          <NumberInputField
            label="Pausenzeit (in Minuten)"
            type="number"
            :default-value="5"
            v-model="specs.breakDuration"
          />
        </div>
      </Card>
      <Card title="Tourniermodus">
        <div class="flex space-x-5">
          <Switch
            v-model="specs.roundRobin"
            :disabled="false"
            label="Round-Robin"
          />
          <Switch
            v-model="specs.groupPhase"
            :disabled="false"
            label="Gruppen- und K.o.-Phase"
          />
        </div>
      </Card>
      <Card v-if="specs.groupPhase" title="Gruppen- und K.o.-Phase">
        <div class="w-full text-sm">
          <div class="flex space-x-2 w-full font-medium">
            <div class="w-1/3">Kategorie</div>
            <div class="w-1/3">Teams / Kategorie</div>
            <div class="w-1/3">Untergruppen</div>
          </div>
          <div
            v-for="category in categories"
            class="flex space-x-2 w-full items-center"
          >
            <div class="w-1/3">{{ category.name }}</div>
            <div class="w-1/3 text-center">{{ category.teams?.length }}</div>
            <div class="w-1/3">
              <NumberInputField
                label=""
                type="number"
                :min="1"
                :default-value="1"
                :max="Math.floor(category.teams?.length! / 2)"
                v-model="specs.groupsPerCategory[category.id]"
                @increment="handleChange($event, category.id, 'increment')"
                @decrement="handleChange($event, category.id, 'decrement')"
              />
            </div>
          </div>
        </div>
      </Card>
    </div>
    <div class="flex flex-col space-y-3 w-full h-full">
      <Card title="Weiter Einstellungen">
        <Switch
          v-model="specs.playForThirdPlace"
          :disabled="false"
          label="Spiel um den 3. Platz ?"
        />
      </Card>
      <Card title="Anzahl Spiele">
        {{ calculation?.gameCount }}
      </Card>
      <Card title="Tournierende">
        {{ calculation?.lastGameTime }}
      </Card>
      <Card title="Tournier erstellen">
        <Button @click="create" variant="default">Spiele erstellen</Button>
      </Card>
    </div>
  </div>
</template>
<script setup lang="ts">
import Card from "@/components/Card.vue";
import Switch from "./Switch.vue";
import { onMounted, ref } from "vue";
import InputField from "./InputField.vue";
import { useApi } from "@/api";
import { watch } from "vue";
import { Button } from "@/components/ui/button";
import { useToast } from "@/components/ui/toast/use-toast";
import NumberInputField from "./NumberInputField.vue";
import { Category } from "@/types";

const { toast } = useToast();

const { data: specs, fetchData: fetchSpecs } =
  useApi<tournamentSpecifications>("tournament/specs");

onMounted(async () => {
  await fetchSpecs(undefined, true);
  console.log(specs);
});

const calculation = ref();

interface tournamentSpecifications {
  startTime: string;
  gameDuration: number;
  breakDuration: number;
  roundRobin: boolean;
  groupPhase: boolean;
  groupsPerCategory: Record<number, number>;
  playForThirdPlace: boolean;
}

const handleChange = (value: number, id: number, type: string) => {
  if (!specs.value) {
    return;
  }
  if (type === "increment") {
    specs.value.groupsPerCategory[id] = 2 ** Math.ceil(Math.log2(value));
  } else {
    specs.value.groupsPerCategory[id] = 2 ** Math.floor(Math.log2(value));
  }
};

watch(
  () => specs.value?.roundRobin,
  (roundRobin) => {
    if (roundRobin && specs.value) {
      specs.value.groupPhase = false;
    }
  },
);

watch(
  () => specs.value?.groupPhase,
  (groupPhase) => {
    if (groupPhase && specs.value) {
      specs.value.roundRobin = false;
    }
  },
);

const { dataList: categories, fetchData: fetchCategoriers } =
  useApi<Category>("categories");

fetchCategoriers().then(() => {
  specs.value!.groupsPerCategory = categories.value.reduce(
    (acc: Record<number, number>, category) => {
      acc[category.id] = 1;
      return acc;
    },
    {} as Record<number, number>,
  );
});

let tournamentCalculateApi = useApi("tournament/calculate");
let tournamentCreateApi = useApi("tournament/create");

const calculate = async () => {
  if (!specs.value) {
    return;
  }
  calculation.value = await tournamentCalculateApi.createData(specs.value);
};

const create = async () => {
  await tournamentCreateApi.createData(specs.value);
  toast({
    title: "Turnier erstellt",
    description: "Das Turnier wurde erfolgreich erstellt",
  });
};

watch(
  specs,
  async () => {
    calculate();
  },
  { deep: true },
);

onMounted(() => {
  calculate();
});
</script>
