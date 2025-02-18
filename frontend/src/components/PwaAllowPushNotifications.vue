<script setup lang="ts">
import { ref, onMounted } from "vue";
import { Button } from "./ui/button";
import { BellIcon } from "lucide-vue-next";

import {
  requestNotificationPermission,
  subscribeUserToPush,
} from "@/pushNotifications";

const pushSupported = ref(false);
const pushSubscribed = ref(false);

const checkPushSupport = async () => {
  if ("Notification" in window && "serviceWorker" in navigator) {
    pushSupported.value = true;
    const registration = await navigator.serviceWorker.ready;
    const subscription = await registration.pushManager.getSubscription();
    pushSubscribed.value = !!subscription;
  }
};

const subscribeToPush = async () => {
  if (!pushSupported.value) return;

  try {
    const allowed = await requestNotificationPermission();
    if (allowed) {
      await subscribeUserToPush();
      pushSubscribed.value = true;
    }
  } catch (error) {
    console.error(error);
  }
};

onMounted(() => {
  checkPushSupport();
});
</script>

<template>
  <Button
    v-if="pushSupported && !pushSubscribed"
    @click="subscribeToPush"
    variant="ghost"
  >
    <BellIcon />
  </Button>
</template>
