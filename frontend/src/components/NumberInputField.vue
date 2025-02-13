<script setup lang="ts">
import { Label } from "@/components/ui/label";
import {
  NumberField,
  NumberFieldContent,
  NumberFieldDecrement,
  NumberFieldIncrement,
  NumberFieldInput,
} from "@/components/ui/number-field";
import { computed } from "vue";

const props = defineProps<{
  label: string;
  modelValue: number;
  min?: number;
  max?: number;
}>();

const emit = defineEmits(["update:modelValue", "decrement", "increment"]);

const value = computed({
  get: () => props.modelValue,
  set: (value: number) => emit("update:modelValue", value),
});
</script>

<template>
  <NumberField v-model="value" :min="props.min" :max="props.max">
    <Label>{{ label }}</Label>
    <NumberFieldContent>
      <NumberFieldDecrement @click="emit('decrement', modelValue)" />
      <NumberFieldInput />
      <NumberFieldIncrement @click="emit('increment', modelValue)" />
    </NumberFieldContent>
  </NumberField>
</template>
