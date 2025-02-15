<script setup lang="ts">
import { Label } from "@/components/ui/label";
import { Switch } from "@/components/ui/switch";
import { computed } from "vue";

const props = defineProps<{
  label: string;
  modelValue: boolean;
  disabled?: boolean;
}>();

const emit = defineEmits(["update:modelValue"]);

const value = computed({
  get: () => props.modelValue,
  set: (newValue) => {
    emit("update:modelValue", newValue);
  },
});

const id = computed(() => props.label.toLowerCase().replace(" ", "-"));
</script>

<template>
  <div class="flex items-center space-x-2">
    <Switch :id="id" v-model:checked="value" :disabled="props.disabled" />
    <Label :for="id">{{ label }}</Label>
  </div>
</template>
