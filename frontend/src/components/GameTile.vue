<template>
    <div @dragstart="handleDragStart" draggable="true" class="rounded-md border p-3 w-full cursor-pointer select-none " @click="handleClick" :style="{backgroundColor: props.game.category.color!}">
        <div class="size-full flex space-x-2">
            <div class="w-full text-right">{{ props.game.teamA?.name }} {{ props.game.pointsTeamA }}</div> <div>:</div><div class="w-full">{{ props.game.pointsTeamB }} {{ props.game.teamB?.name }}</div>
        </div>
        
    </div>
</template>
<script setup lang="ts">
import { Game } from '@/types';

const props = defineProps<{
    game: Game;
}>();

const emit = defineEmits(['clickOnGame']);

const handleClick = () => {
    emit('clickOnGame', props.game.id);
}

const handleDragStart = (event: DragEvent) => {
    event.dataTransfer?.setData('gameId', props.game.id.toString());
    console.log("Gotgameid", event.dataTransfer?.getData('gameId'));
}

</script>