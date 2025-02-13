<script setup lang="ts">
import { useRoute } from "vue-router";
const route = useRoute();
import { useApi } from "@/api";
import { Game } from "@/types";
const { data: game, fetchData: fetchGame, updateData : updateGame } = useApi<Game>("games");
import { Button } from "@/components/ui/button";
import { PlusIcon, MinusIcon, PlayIcon, RotateCcwIcon, PauseIcon } from "lucide-vue-next";
import { useOptionsStore } from '@/stores/options';
const optionsStore = useOptionsStore();
import { ref, onBeforeMount } from 'vue';
const timer = ref(0);

onBeforeMount(async ()=>{
    await optionsStore.fetchOptions();
    timer.value = optionsStore.getGameDuration * 60 * 1000
}

)

import {format} from 'date-fns';

fetchGame(route.params.id as string);

const changePoints = async (points: number, team: "A" | "B") => {
    if(team === "A") {
        game.value!.pointsTeamA += points
        await updateGame(game.value!.id, { pointsTeamA: game.value!.pointsTeamA });
    } else {
        game.value!.pointsTeamB += points
        await updateGame(game.value!.id, { pointsTeamB: game.value!.pointsTeamB });
    }
  
};

let interval :  NodeJS.Timeout = setInterval(()=>{}, 0);
const showPlay = ref(true);

const startTimer = () => {
    showPlay.value = false;
    interval = setInterval(() => {
        if(timer.value <= 0) {
            timer.value = 0;
            stopTimer();
            return;
        }
        timer.value -= 1000;
    }, 1000);
}

const stopTimer = () => {
    showPlay.value = true;
    clearInterval(interval);
}

const restartTimer = () => {
    timer.value = optionsStore.getGameDuration * 60 * 1000;
    clearInterval(interval);
    showPlay.value = true;
}

</script>

<template>
  <div class="p-5 h-screen  flex justify-center items-center">
    <div
      class="flex flex-col h-full w-full justify-between"
      
    >
      <div class="w-full flex justify-around text-2xl font-semibold break-all space-x-2">
        <p>{{ game?.teamA.name }}</p> <p>vs.</p> <p>{{ game?.teamB.name }}</p>
      </div>
      <div class="w-full flex justify-around text-2xl font-semibold break-all space-x-2">
        <p>{{ game?.teamA.section?.name }}</p> <p></p> <p>{{ game?.teamB.section?.name }}</p>
      </div>
      <div>
        <div class="flex justify-center font-semibold text-4xl">Zeit</div>
        <div class="flex justify-center font-semibold text-7xl">{{ format(timer, 'mm:ss') }}</div>
      </div>
      <div>
        <div class="flex justify-center font-semibold text-4xl">Punkte</div>
        <div class="flex justify-around font-semibold">
          <Button @click="changePoints(1, 'A')" class="h-full aspect-square"  variant="ghost"><PlusIcon class="size-24" /></Button>
          <Button @click="changePoints(1, 'B')" class="h-full aspect-square" variant="ghost"><PlusIcon class="size-24" /></Button>
        </div>
        <div class="flex justify-around font-semibold text-9xl">
          <p>{{ game?.pointsTeamA }}</p>
          <p>{{ game?.pointsTeamB }}</p>
        </div>
        <div class="flex justify-around font-semibold">
          <Button @click="changePoints(-1, 'A')" class="h-full aspect-square"  variant="ghost"><MinusIcon class="size-24" /></Button>
          <Button @click="changePoints(-1, 'B')" class="h-full aspect-square" variant="ghost"><MinusIcon class="size-24" /></Button>
        </div>
      </div>
      <div class="flex justify-around font-semibold">
        <Button @click="restartTimer" class="h-full aspect-square" variant="ghost"><RotateCcwIcon class="size-24" /></Button>
          <Button v-if="showPlay" @click="startTimer" class="h-full aspect-square"  variant="ghost"><PlayIcon class="size-24" /></Button>
          <Button v-else @click="stopTimer" class="h-full aspect-square"  variant="ghost"><PauseIcon class="size-24" /></Button>
        </div>
      <Button>Spielstand abschicken</Button>
    </div>
  </div>
</template>

