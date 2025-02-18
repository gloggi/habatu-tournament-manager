<script setup lang="ts">
import { ref, onMounted, onBeforeUnmount } from "vue";

const deferredPrompt = ref<Event | null>(null);
const installAvailable = ref(false);

const handleBeforeInstallPrompt = (event: Event) => {
  event.preventDefault();
  deferredPrompt.value = event;
  installAvailable.value = true;
};

const installPWA = async () => {
  if (deferredPrompt.value) {
    const promptEvent = deferredPrompt.value as any;
    promptEvent.prompt();
    const choiceResult = await promptEvent.userChoice;

    if (choiceResult.outcome === "accepted") {
      console.log("PWA Installed");
    } else {
      console.log("PWA Installation Dismissed");
    }

    deferredPrompt.value = null;
    installAvailable.value = false;
  }
};

// Attach and clean up event listeners
onMounted(() => {
  window.addEventListener("beforeinstallprompt", handleBeforeInstallPrompt);
});

onBeforeUnmount(() => {
  window.removeEventListener("beforeinstallprompt", handleBeforeInstallPrompt);
});

import { Button } from "./ui/button";
import { DownloadIcon } from "lucide-vue-next";
</script>

<template>
  <Button v-if="installAvailable" @click="installPWA" variant="ghost">
    <DownloadIcon />
  </Button>
</template>
