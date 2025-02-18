<script setup lang="ts">
import { ref, onMounted, onUnmounted } from "vue";
import { format } from "date-fns";

import logo from "@/assets/rotating_ball_a.png";
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

import PwaInstallButton from "./PwaInstallButton.vue";
import PwaAllowPushNotifications from "./PwaAllowPushNotifications.vue";

const userStore = useUserStore();
</script>

<template>
  <header
    class="sticky z-40 top-0 bg-background/80 backdrop-blur-lg border-b border-border"
  >
    <div
      class="container flex h-14 max-w-screen-2xl justify-between items-center w-full"
    >
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
          <img class="aspect-square" :src="logo" />
        </Button>
      </div>
      <div class="text-2xl font-semibold flex items-center space-x-2">
        <ClockIcon /><span>{{ time }}</span>
      </div>
      <div class="flex space-x-2">
        <PwaInstallButton />
        <PwaAllowPushNotifications />
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
  </header>
</template>
