<script setup lang="ts">
import Toaster from '@/components/ui/toast/Toaster.vue'
import { useUserStore } from '@/stores/user';
import { useOptionsStore } from '@/stores/options';
const userStore = useUserStore();
const optionsStore = useOptionsStore();

import { onBeforeMount } from 'vue';

onBeforeMount(async () => {
  await userStore.fetchUser();
  await optionsStore.fetchOptions();
});

</script>

<template>
  <RouterView />
  <Toaster />
</template>

