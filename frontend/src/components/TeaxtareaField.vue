<script setup lang="ts">
import { defineEmits, computed } from 'vue'
import { Label } from '@/components/ui/label'
import { Textarea } from '@/components/ui/textarea'

const props = defineProps<{
  label: string;
  required?: boolean;
  modelValue?: string;
  defaultValue?: string;
}>();

const emit = defineEmits(['update:modelValue'])

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
    <Label :for="label.toLowerCase()">{{ label }} {{ required ? '*' : '' }}</Label>
    <Textarea v-model="payload" :id="label.toLowerCase()" :placeholder="label" :default-value="props.defaultValue" class="resize-none" />
  </div>
</template>
