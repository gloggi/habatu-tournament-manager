<script setup lang="ts">
import Container from "@/components/Container.vue";
import H1 from "@/components/H1.vue";
import { useUserStore } from "@/stores/user";
import InputField from "@/components/InputField.vue";
import ComboBox from "@/components/ComboBox.vue";
import { Button } from "@/components/ui/button";
import { RefreshCw, LogOut } from "lucide-vue-next";
import { useApi } from "@/api";
import { User } from "@/types";
import { onMounted } from "vue";
import {
  requestNotificationPermission,
  subscribeUserToPush,
} from "@/pushNotifications";
const { updateData: updateUser } = useApi<User>("users");

const { data: user, fetchData: fetchUser } = useApi<User>("auth");

const userStore = useUserStore();

const updateAndFetch = async () => {
  if (user.value) {
    await updateUser(user.value.id, user.value);
    await fetchUser(undefined, true);
  }
};

const enablePushNotifications = async () => {
  const permissionGranted = await requestNotificationPermission();
  if (permissionGranted) {
    await subscribeUserToPush();
  }
};

onMounted(async () => {
  await fetchUser(undefined, true);
  await enablePushNotifications();
});

const logout = () => {
  userStore.logout();
};
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
      <Button><RefreshCw class="w-4 h-4 mr-2" /> Update</Button>
    </form>

    <Button @click="logout" variant="destructive" class="mt-5 w-full"
      ><LogOut class="w-4 h-4 mr-2" /> Logout</Button
    >
  </Container>
</template>
