<template>
  <div
    @dragenter="handleDragEnter"
    @dragover="handleDragOver"
    @drop="handleDrop"
    @dragleave="handleDragLeave"
    class="w-full h-full rounded-md md:min-h-11"
    :class="{ 'border-2 border-gray-400 border-spacing-5 border-dashed': dragActive, 'h-28': dragActive }"
  >
    <GameTile
      v-for="game in props.scheduleEntry.games"
      @clickOnGame="handleOpenGameSheet"
      :game="game"
    />
  </div>
</template>

<script setup lang="ts">
import GameTile from "@/components/GameTile.vue";
import { Game, ScheduleEntry } from "@/types";
import { ref } from "vue";
import { useApi } from "@/api";

const props = defineProps<{
  scheduleEntry: ScheduleEntry
}>();


const emit = defineEmits(["clickOnGame", "change"]);

const dragActive = ref(false);
let dragCounter = 0;

const handleDragEnter = (event: DragEvent) => {
  event.preventDefault();
  dragCounter++;
  dragActive.value = true;
};

const handleDragOver = (event: DragEvent) => {
  event.preventDefault();
};

const handleDragLeave = (event: DragEvent) => {
  event.preventDefault();
  dragCounter--;
  if (dragCounter === 0) {
    dragActive.value = false;
  }
};

const { updateData: updateGame} = useApi<Game>("games");
const handleDrop = async (event: DragEvent) => {
  handleDragLeave(event);
  const gameID = parseInt(event.dataTransfer?.getData("gameId")!);
  await updateGame(gameID, { hallId: props.scheduleEntry.slotInfo.hallId, timeslotId: props.scheduleEntry.slotInfo.timeslotId });
  emit("change");

};

const handleOpenGameSheet = (gameId: number) => {
  emit("clickOnGame", gameId);
};
</script>