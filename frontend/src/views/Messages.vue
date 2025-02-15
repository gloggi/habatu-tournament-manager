<script setup lang="ts">
import ComboBox from "@/components/ComboBox.vue";
import InputField from "@/components/InputField.vue";
import TeaxtareaField from "@/components/TeaxtareaField.vue";
import { Button } from "@/components/ui/button";

import { Message } from "@/types";

import { useApi } from "@/api";
import H1 from "@/components/H1.vue";
import { ref } from "vue";

import { useToast } from "@/components/ui/toast";
import Switch from "@/components/Switch.vue";

const { toast } = useToast();

const { createData: sendUnicast } = useApi<Message>("messages/unicast");
const { createData: sendBroadcast } = useApi<Message>("messages/broadcast");

const broadcast = ref(false);

const message = ref<Message>({
  userId: 0,
  title: "",
  body: "",
});

const processForm = async () => {
  try {
    if (broadcast.value) {
      await sendBroadcast(message.value);
    } else {
      await sendUnicast(message.value);
    }
    message.value = {
      userId: 0,
      title: "",
      body: "",
    };
    toast({
      title: "Erfolg",
      description: "Die Nachricht wurde verschickt",
      variant: "default",
    });
  } catch (e) {
    console.error(e);
    toast({
      title: "Fehler",
      description: "Die Nachricht konnte nicht verschickt werden",
      variant: "destructive",
    });
    return;
  }
};
</script>
<template>
  <div id="full-height" class="flex-1 flex items-center justify-center">
    <div class="w-2/3 flex flex-col space-y-2">
      <H1>Verschick eh Nachricht</H1>
      <form @submit.prevent="processForm" class="flex flex-col space-y-2">
        <div class="flex space-x-2 h-full items-center">
          <ComboBox
          class="w-full"
            v-model="message.userId"
            display-key="nickname"
            label="An"
            options-entity="users"
          />
          <Switch v-model="broadcast" label="An alle schicken" />
        </div>
        <InputField v-model="message.title" label="Titel" type="text" />

        <TeaxtareaField label="Nachricht" v-model="message.body" />
        <Button>Senden</Button>
      </form>
    </div>
  </div>
</template>
