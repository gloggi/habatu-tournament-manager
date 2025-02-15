<script setup lang="ts">
import Container from "@/components/Container.vue";
import H1 from "@/components/H1.vue";
import { useUserStore } from "@/stores/user";
import InputField from "@/components/InputField.vue";
import ComboBox from "@/components/ComboBox.vue";
import { Button } from "@/components/ui/button";
const userStore = useUserStore();
import { useApi } from "@/api";
import { User } from "@/types";
import { onMounted } from "vue";
import {
  requestNotificationPermission,
  subscribeUserToPush,
} from "@/pushNotifications";
const {
  fetchData: fetchUser,
  updateData: updateUser,
  data: user,
} = useApi<User>("users");
fetchUser(userStore.getUserId);

const updateAndFetch = async () => {
  if (user.value) {
    await updateUser(user.value.id, user.value);
    await fetchUser(user.value.id);
  }
};

const enablePushNotifications = async () => {
  const permissionGranted = await requestNotificationPermission();
  if (permissionGranted) {
    await subscribeUserToPush();
  }
};

onMounted(async () => {
  await enablePushNotifications();
});
</script>
<template>
  <Container>
    <H1 class="mb-5">Profil bearbeite</H1>
    <form
      class="flex flex-col space-y-2"
      v-if="user"
      @submit.prevent="updateAndFetch"
    >
      <InputField v-model="user.nickname" label="Pfadiname" />
      <ComboBox v-model="user.teamId" label="Team" optionsEntity="teams" />
      <ComboBox
        v-model="user.sectionId"
        label="Abteilung"
        optionsEntity="sections"
      />
      <Button>Update</Button>
    </form>
  </Container>
</template>
