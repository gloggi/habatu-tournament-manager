<script setup lang="ts">
import { ref, onMounted, onUnmounted } from "vue";
import { format } from "date-fns";

import rotatingBall from "@/assets/rotating_ball_a.png";
import voelkBall from "@/assets/voelk_ball.png";
import { Avatar } from "@/components/ui/avatar";
import { Button } from "./ui/button";
import { ChevronLeftIcon, ClockIcon } from "lucide-vue-next";

const time = ref(format(new Date(), "HH:mm:ss"));

const updateTime = () => {
  time.value = format(new Date(), "HH:mm:ss");
};
let interval: NodeJS.Timeout;
onMounted(() => {
  interval = setInterval(updateTime, 1000);
});

onUnmounted(() => {
  clearInterval(interval);
});

import { useUserStore } from "@/stores/user";
import GameTableControlButtons from "@/components/GameTableControlButtons.vue";

const userStore = useUserStore();
</script>

<template>
  <header
    class="sticky z-40 top-0 bg-background/80 backdrop-blur-lg border-b border-border"
  >
    <div
      class="container flex h-14 max-w-screen-2xl justify-between items-center w-full"
    >
      <div class="w-1/3">
        <div class="flex items-center h-full space-x-2">
          <Button
            @click="() => $router.back()"
            class="md:hidden aspect-square p-0"
            variant="ghost"
            ><ChevronLeftIcon
          /></Button>
          <Button
            @click="() => $router.push('/')"
            class="aspect-square p-0"
            variant="ghost"
          >
            <img
              v-if="$env.VITE_SHORT_NAME === 'habatu'"
              class="aspect-square"
              :src="rotatingBall"
            />
            <img
              v-else-if="$env.VITE_SHORT_NAME === 'voelk'"
              class="aspect-square"
              :src="voelkBall"
            />
          </Button>
        </div>
      </div>
      <div class="w-1/3 flex justify-center">
        <div class="text-2xl font-semibold flex items-center space-x-2">
          <ClockIcon class="hidden md:block" /><span>{{ time }}</span>
        </div>
      </div>
      <div class="w-1/3 flex justify-end">
        <div class="flex space-x-2">
          <GameTableControlButtons
            class="hidden md:flex"
            v-if="$route.name === 'GameTable'"
          />
          <Button
            @click="() => $router.push('/profile')"
            class="aspect-square p-0"
            variant="ghost"
          >
            <Avatar>
              {{ userStore.user?.nickname.substring(0, 1).toUpperCase() }}
            </Avatar>
          </Button>
        </div>
      </div>
    </div>
  </header>
</template>
