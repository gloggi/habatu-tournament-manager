<script setup lang="ts">
import {
  Card,
  CardContent,
  CardDescription,
  CardFooter,
  CardHeader,
  CardTitle,
} from "@/components/ui/card";

const props = defineProps<{
  title?: string;
  description?: string;
  footer?: string;
}>();
</script>
<template>
  <Card>
    <CardHeader v-if="props.title || props.description">
      <CardTitle>{{ props.title }}</CardTitle>
      <CardDescription>{{ props.description }}</CardDescription>
    </CardHeader>
    <CardContent class="w-full h-full">
      <slot></slot>
    </CardContent>
    <CardFooter v-if="props.footer">
      {{ props.footer }}
    </CardFooter>
  </Card>
</template>
