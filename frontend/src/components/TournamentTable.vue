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
          class="flex flex-col md:flex-row w-full space-y-2 space-x-2"
          v-if="
            Object.values(timeslot).some((entry) => entry.slotInfo.hasGames)
          "
        >
          <div
            class="w-full mt-5 md:mt-0 md:w-1/4 font-medium text-xl md:text-base"
          >
            <TimeTile :time="time" />
          </div>
          <div v-for="scheduleEntry in timeslot" class="flex w-full">
            <div
              class="flex flex-col w-full"
              v-if="scheduleEntry.slotInfo.hasGames"
            >
              <p class="md:hidden font-medium text-sm">
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

import TimeTile from "@/components/TimeTile.vue";
import TimeHallSlot from "@/components/TimeHallSlot.vue";

const emit = defineEmits(["clickOnGame"]);


const tableTypeUrlMapping = {
  [TableType.Normal]: "tournament/table",
  [TableType.Referee]: "tournament/referee-table",
  [TableType.Team]: "tournament/team-table",
};

const { fetchData: fetchTournamentTable, data: table } = useApi<Schedule>(
  tableTypeUrlMapping[tableType]
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
  }
);
const handleOpenGameSheet = (gameId: number) => {
  emit("clickOnGame", gameId);
};
</script>
