<template>
  <Container>
    <div class="w-full space-x-2 hidden md:flex font-medium text-xl">
      <div class="w-1/4"></div>
      <div v-for="hall in halls" class="w-full flex justify-center">
        <p>
          {{ hall.name }}
        </p>
      </div>
    </div>
    <div class="flex flex-col">
      <template v-for="(timeslot, time) in table">
        <div
          class="flex flex-col md:flex-row w-full space-y-2 space-x-2 relative"
          :class="{
            'hidden md:flex': !Object.values(timeslot).some(
              (entry) => entry.slotInfo.hasGames,
            ),
          }"
        >
          <div
            class="w-full mt-5 md:mt-0 md:w-1/4 font-medium text-xl md:text-base"
          >
            <div
              v-if="timeIsNow(time)"
              class="absolute -z-10 inset-0 top-2 ring-4 ring-foreground ring-offset-1 rounded-md ml-2 -mr-2 md:ml-0 md:-mr-0"
            ></div>
            <TimeTile :time="time" />
          </div>
          <div v-for="scheduleEntry in timeslot" class="flex w-full">
            <div class="flex flex-col w-full">
              <p
                v-if="scheduleEntry.slotInfo.hasGames"
                class="md:hidden font-medium text-sm"
              >
                {{ scheduleEntry.slotInfo.hallName }}
              </p>
              <TimeHallSlot
                @change="updateTable"
                :scheduleEntry="scheduleEntry"
                @clickOnGame="handleOpenGameSheet"
              />
            </div>
          </div>
        </div>
      </template>
      <Button
        @click="handleAddTimeslot"
        variant="ghost"
        class="hidden md:flex md:w-1/12 items-center"
        ><PlusIcon class="size-4"
      /></Button>
    </div>
  </Container>
</template>
<script setup lang="ts">
import { TableType } from "@/types";
const props = defineProps<{
  tableType?: TableType;
}>();

const tableType = props.tableType ?? TableType.Normal;

import Container from "@/components/Container.vue";
import { useApi } from "@/api";
import { onMounted } from "vue";
import { Schedule, Hall } from "@/types";
import { ref, watch } from "vue";
import { Button } from "./ui/button";

import TimeTile from "@/components/TimeTile.vue";
import TimeHallSlot from "@/components/TimeHallSlot.vue";

const emit = defineEmits(["clickOnGame"]);

const tableTypeUrlMapping = {
  [TableType.Normal]: "tournament/table",
  [TableType.Referee]: "tournament/referee-table",
  [TableType.Team]: "tournament/team-table",
};

const { fetchData: fetchTournamentTable, data: table } = useApi<Schedule>(
  tableTypeUrlMapping[tableType],
);

const { fetchData: fetchHalls, dataList: halls } = useApi<Hall>("halls");

onMounted(() => {
  fetchTournamentTable(undefined, true);
  fetchHalls();
});

const updateTable = async () => {
  fetchTournamentTable(undefined, true);
};

const showGameSheet = ref(false);

watch(
  () => showGameSheet.value,
  async (value) => {
    if (!value) {
      await updateTable();
    }
  },
);
const handleOpenGameSheet = (gameId: number) => {
  emit("clickOnGame", gameId);
};
import { isWithinInterval, set, startOfMinute } from "date-fns";
import { useOptionsStore } from "@/stores/options";

const optionsStore = useOptionsStore();

const timeIsNow = (timeslot: string): boolean => {
  const [startHour, startMin, endHour, endMin] = timeslot
    .split("_")
    .map(Number);

  const now = new Date();

  const startTime = set(now, {
    hours: startHour,
    minutes: startMin - optionsStore.getBreakDuration,
    seconds: 0,
    milliseconds: 0,
  });
  const endTime = set(now, {
    hours: endHour,
    minutes: endMin,
    seconds: 0,
    milliseconds: 0,
  });
  return isWithinInterval(now, {
    start: startOfMinute(startTime),
    end: startOfMinute(endTime),
  });
};

import { PlusIcon } from "lucide-vue-next";

const { createData: addTimeslot } = useApi("tournament/new-timeslot");

const handleAddTimeslot = async () => {
  await addTimeslot({});
  await updateTable();
};

import { storeToRefs } from "pinia";
const { getLocalGameTableRefreshRate } = storeToRefs(optionsStore);

function refreshLoop() {
  if (getLocalGameTableRefreshRate.value > 0) {
    updateTable();
    setTimeout(refreshLoop, getLocalGameTableRefreshRate.value * 1000);
  } else {
    setTimeout(refreshLoop, 10000);
  }
}

refreshLoop();
</script>
