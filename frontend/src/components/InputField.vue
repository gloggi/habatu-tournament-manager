<script setup lang="ts">
import { defineEmits, computed } from 'vue'
import { Label } from '@/components/ui/label'
import { Input } from '@/components/ui/input'
const props = defineProps<{
  label: string;
  required?: boolean;
  type?: string;
  modelValue?: number | string;
  defaultValue?: string | number;
}>();

const emit = defineEmits(['update:modelValue'])

// Define a computed property 'payload' with getter and setter
const payload = computed({
  get() {
    return props.modelValue
  },
  set(value: string) {
    emit('update:modelValue', value)
  }
})
</script>

<template>
  <div class="w-full flex flex-col space-y-2">
    <Label :for="label.toLowerCase()">{{label}} {{required ? '*' : ''}}</Label>
    <Input v-model="payload" :id="label.toLowerCase()" :type="type" :placeholder="label" :default-value="props.defaultValue" />
  </div>
</template>