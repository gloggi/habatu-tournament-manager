<script setup lang="ts">
import Container from "@/components/Container.vue";
import H1 from "@/components/H1.vue";
import { Button } from "@/components/ui/button";
import { Megaphone, MegaphoneOff } from "lucide-vue-next";
import { useToast } from "@/components/ui/toast/use-toast";
import { useApi } from "@/api";

const { createData: sendRefereeAssignment } = useApi("actions/assign-referees");
const { createData: clearRefereeAssignment } = useApi("actions/clear-referees");

const { toast } = useToast();

const assignReferees = async () => {
  try {
    await sendRefereeAssignment({});
    toast({
      title: "Schiris zugeordnet",
    });
  } catch (e: any) {
    toast({
      title: "Fehler beim Zuteilen der Schiris",
      description: e.response.data.message,
      variant: "destructive",
    });
    return;
  }
};

const clearReferees = async () => {
  try {
    await clearRefereeAssignment({});
    toast({
      title: "Schiris entfernt",
    });
  } catch (e: any) {
    toast({
      title: "Fehler beim Entfernen der Schiris",
      description: e.response.data.message,
      variant: "destructive",
    });
    return;
  }
};
</script>

<template>
  <Container>
    <H1 class="mb-8">Settings</H1>
    <h2 class="text-3xl font-bold">Aktionen</h2>
    <div class="flex flex-col space-y-3">
      <h2 class="text-xl font-medium">Schiris Zuteilen</h2>
      <p class="text-sm text-muted-foreground">
        Sobald dieser Knopf gedrückt wird, werden alle Schiris auf die Spiele
        verteilt.
      </p>
      <div class="flex space-x-2">
        <Button class="w-fit" @click="assignReferees">
          <Megaphone class="w-6 h-6 mr-2" />
          Schiris zuteilen</Button
        >
        <Button class="w-fit" @click="clearReferees">
          <MegaphoneOff class="w-6 h-6 mr-2" />
          Schiris entfernen</Button
        >
      </div>

      <div
        data-orientation="horizontal"
        role="separator"
        class="shrink-0 bg-border relative h-px w-full"
      ></div>
    </div>
  </Container>
</template>
