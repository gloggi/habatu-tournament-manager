<script setup lang="ts">
import { Button } from "@/components/ui/button";
import pbs from "../assets/pbs.svg";
</script>
<template>
  <Button class="bg-[#622648] hover:bg-[#81516d] flex space-x-5 w-full">
    <img :src="pbs" class="size-8 p-1" alt="MiData Logo" />
    <span>Mit MiData einloggen</span>
  </Button>
</template>
