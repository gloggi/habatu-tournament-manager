
<script setup lang="ts">
import TournamentTable from '@/components/TournamentTable.vue';
import GameSheet from "@/components/GameSheet.vue";
import { ref } from 'vue';
const showGameSheet = ref(false);
const sheetGameId = ref(0);
import { useUserStore } from '@/stores/user';

const userStore = useUserStore();

const handleClickOnGame = (gameId: number) => {
    if (!userStore.isAdmin) {
        return;
    }
    sheetGameId.value = gameId;
    showGameSheet.value = true;
}
</script>
<template>
        <TournamentTable @clickOnGame="handleClickOnGame" />
        <GameSheet v-model="showGameSheet" :gameId="sheetGameId" />
</template>