<script setup lang="ts">
import { ref } from "vue";
import {
  Drawer,
  DrawerContent,
  DrawerDescription,
  DrawerFooter,
  DrawerHeader,
  DrawerTitle,
} from "@/components/ui/drawer";

import { Button } from "@/components/ui/button";

import ComboBox from "./ComboBox.vue";
import { useApi } from "@/api";
import { User } from "@/types";
import { onMounted, watch } from "vue";
import PwaAllowPushNotifications from "./PwaAllowPushNotifications.vue";

const { updateData: updateUser } = useApi<User>("users");

const { data: user, fetchData: fetchUser } = useApi<User>("auth");

import { useRoute } from "vue-router";

const route = useRoute();
const isOpen = ref(false);
const handleFirstLogin = async () => {
  const login = route.query.login;
  if (login) {
    await fetchUser(undefined, true);
    isOpen.value = true;
  }
};

onMounted(async () => {
  await handleFirstLogin();
});

watch(
  () => user.value?.teamId,
  async (newTeamId, oldTeamId) => {
    if (newTeamId !== oldTeamId) {
      if (user.value) {
        await updateUser(user.value.id, { teamId: newTeamId });
        await fetchUser(undefined, true);
      }
    }
  },
);

watch(
  () => user.value?.sectionId,
  async (newSectionId, oldSectionId) => {
    if (newSectionId !== oldSectionId) {
      if (user.value) {
        await updateUser(user.value.id, { sectionId: newSectionId });
        await fetchUser(undefined, true);
      }
    }
  },
);

import { useRouter } from "vue-router";
const router = useRouter();

const done = () => {
  isOpen.value = false;
  router.push({ name: "Home" });
};
</script>

<template>
  <Drawer :open="isOpen">
    <DrawerContent>
      <DrawerHeader v-if="user">
        <DrawerTitle>Wähl dis Team und dini Abteilig us</DrawerTitle>
        <DrawerDescription>
          Wähl dis Team und dini Abteilig us, sodass du de Spielplan vo dim Team
          gsehsch. Du chasch die ihstellig immerno ändere wenn udf d Chachle
          "Mis Profil" tippsch.
        </DrawerDescription>
        <ComboBox
          v-model="user.teamId"
          label="Mis Team"
          optionsEntity="teams"
        />
        <ComboBox
          v-model="user.sectionId"
          label="Mini Abteilung"
          optionsEntity="sections"
        />
        <DrawerTitle>Push Benachrichtigunge Aktiviere</DrawerTitle>
        <DrawerDescription
          >Aktivier Push Benachrichtigunge, sodass du immer informiert wirsch,
          sobald s nächschte Spiel ahstaht.
        </DrawerDescription>
        <PwaAllowPushNotifications />
      </DrawerHeader>
      <DrawerFooter>
        <Button @click="done">Gmacht</Button>
      </DrawerFooter>
    </DrawerContent>
  </Drawer>
</template>
