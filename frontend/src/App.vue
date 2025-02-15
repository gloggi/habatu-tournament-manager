<script setup lang="ts">
import Toaster from "@/components/ui/toast/Toaster.vue";
import { useUserStore } from "@/stores/user";
import { useOptionsStore } from "@/stores/options";
const userStore = useUserStore();
const optionsStore = useOptionsStore();
import Header from "@/components/Header.vue";

import { onBeforeMount } from "vue";

onBeforeMount(async () => {
  await userStore.fetchUser();
  await optionsStore.fetchOptions();
});
</script>

<template>
  <div class="min-h-screen flex flex-col">
    <template
      v-if="$route.name !== 'Login' && !$route.path.startsWith('/admin')"
    >
      <Header />
    </template>
    <RouterView class="h-full" />
    <Toaster />
  </div>
</template>
