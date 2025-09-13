<template>
  <div
    @dragstart="handleDragStart"
    draggable="true"
    class="rounded-md border p-3 w-full cursor-pointer select-none relative"
    :class="{ 'pt-4 pb-2 ': game.finalTypeLabel }"
    @click="handleClick"
  >
    <div class="absolute z-10 top-0 left-0 right-0">
      <p class="font-semibold p-0.5 text-xs flex w-full justify-center">
        {{ game.finalTypeLabel }}
      </p>
    </div>
    <div
      class="absolute -z-20 inset-0 rounded-md"
      :style="{ backgroundColor: tileColor || 'transparent' }"
    ></div>
    <div :class="`absolute -z-10  inset-0 rounded-md ${game.classes}`"></div>
    <div class="size-full flex space-x-2">
      <div class="w-full text-right">
        {{ props.game.teamA?.name }} {{ props.game.pointsTeamA }}
      </div>
      <div>:</div>
      <div class="w-full">
        {{ props.game.pointsTeamB }} {{ props.game.teamB?.name }}
      </div>
    </div>
  </div>
</template>
<script setup lang="ts">
import { Game } from "@/types";
import { useTournamentTableStore } from "@/stores/tournamentTable";
import { storeToRefs } from "pinia";
import { computed } from "vue";

const tournamentTableStore = useTournamentTableStore();
const { showRefereeView, showPlayedView } = storeToRefs(tournamentTableStore);

const props = defineProps<{
  game: Game;
}>();

const emit = defineEmits(["clickOnGame"]);

const handleClick = () => {
  emit("clickOnGame", props.game.id);
};

const handleDragStart = (event: DragEvent) => {
  event.dataTransfer?.setData("gameId", props.game.id.toString());
};

const tileColor = computed(() => {
  if (showRefereeView.value && !props.game.hasReferee) return "gray";
  if (showPlayedView.value && !props.game.played) return "gray";
  return props.game.category.color;
});
</script>
<style>
/* Style is located in the index.css with @apply */
</style>
