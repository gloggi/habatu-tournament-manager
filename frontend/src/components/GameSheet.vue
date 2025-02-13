<script setup lang="ts">

import {
  Sheet,
  SheetContent,
  SheetDescription,
  SheetHeader,
  SheetTitle,
} from '@/components/ui/sheet'
import { computed } from 'vue';
import { useApi } from '@/api';
import { Game } from '@/types';
import { watch } from 'vue';
import NumberInputField  from '@/components/NumberInputField.vue';
import {Button} from '@/components/ui/button';
import MultiComboBox from '@/components/MultiComboBox.vue';
import Switch from './Switch.vue';

const { fetchData: fetchGame, data: game, updateData: updateGame } = useApi<Game>("games");

const props = defineProps<{
  modelValue: boolean;
  gameId: number;
}>();

const emit = defineEmits(['update:modelValue']);

watch(() => props.gameId, (gameId) => {
  fetchGame(gameId);
});

const open = computed({
  get: () => props.modelValue,
  set: (value: boolean) => emit('update:modelValue', value),
});

const referees = computed({
  get: ()=>{
    console.log('value',game.value!.referees);
    return game.value?.referees?.map((referee) => ({id: referee.id, label: referee.nickname})) || [];
  },
  set: (value) => {
    game.value!.referees = value!.map((referee) => ({id: referee.id, nickname: referee.label}));
  }
})

const handleSubmit = async () => {
    if (!game.value) return;

  await updateGame(game.value.id, { pointsTeamA: game.value.pointsTeamA, pointsTeamB: game.value.pointsTeamB, referees: game.value.referees, played: game.value.played });
  open.value = false;
  
}

watch(() => game.value?.pointsTeamA, (newValue) => {
  if (newValue && newValue >0) {
    game.value!.played = true;
  }
});

watch(() => game.value?.pointsTeamB, (newValue) => {
  if (newValue && newValue >0) {
    game.value!.played = true;
  }
});

</script>

<template>
  <Sheet v-model:open="open">
    <SheetContent v-if="game">
      <SheetHeader>
        <SheetTitle>{{game.teamA?.name}} vs. {{ game.teamB?.name }}</SheetTitle>
        <SheetDescription>
        </SheetDescription>
      </SheetHeader>
      <form class="flex flex-col space-y-2" @submit.prevent="handleSubmit">
        <NumberInputField v-if="game.teamA" :label="`Pünkt Team ${game.teamA.name} (${game.teamA.section?.name})`" v-model="game.pointsTeamA" :min="0" />
        <NumberInputField  v-if="game.teamB" :label="`Pünkt Team ${game.teamB.name} (${game.teamB.section?.name})`" v-model="game.pointsTeamB" :min="0" />
        <MultiComboBox v-if="game" label="Schiri" optionsEntity="users" valueKey="nickname" v-model="referees" />
        <Switch v-model="game.played" label="Spiel gespielt" />
        <Button>Speicherä</Button>
      </form>
    </SheetContent>
  </Sheet>
</template>