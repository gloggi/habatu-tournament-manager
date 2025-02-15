<script setup lang="ts">
import { Button } from "@/components/ui/button";

import {
  Command,
  CommandEmpty,
  CommandGroup,
  CommandInput,
  CommandItem,
  CommandList,
} from "@/components/ui/command";
import {
  Popover,
  PopoverContent,
  PopoverTrigger,
} from "@/components/ui/popover";
import { cn } from "@/lib/utils";
import { Check, ChevronsUpDown } from "lucide-vue-next";
import { onMounted, ref } from "vue";
import { useApi } from "@/api";
import { computed } from "vue";
import { Label } from "@/components/ui/label";

import { PropType } from "vue";

const props = defineProps({
  label: {
    type: String as PropType<string>,
    required: true,
  },
  optionsEntity: {
    type: String as PropType<string>,
    required: true,
  },
  modelValue: {
    type: [String, Number] as PropType<string | number | undefined>,
    required: true,
  },
  displayKey: {
    type: String as PropType<string>,
    default: "name",
  },
});

const emit = defineEmits(["update:modelValue"]);

const api = useApi(props.optionsEntity);

onMounted(() => {
  api.fetchData();
});

const options = computed(() => {
  const { dataList } = api;
  return dataList.value.map((item: any) => ({
    value: item.id,
    label: item[props.displayKey],
  }));
});

const open = ref(false);
const value = computed({
  get: () => {
    return options.value.find((option) => option.value === props.modelValue)
      ?.label;
  },
  set: (newValue) => {
    const emitValue = options.value.find(
      (option) => option.label === newValue,
    )?.value;
    emit("update:modelValue", emitValue);
  },
});
</script>

<template>
  <div class="block">
    <Label for="combobox">{{ props.label }}</Label>
    <Popover v-model:open="open">
      <PopoverTrigger as-child>
        <Button
          variant="outline"
          role="combobox"
          :aria-expanded="open"
          class="w-full justify-between"
        >
          {{
            value
              ? options.find((option) => option.label === value)?.label
              : "Select option..."
          }}

          <ChevronsUpDown class="ml-2 h-4 w-4 shrink-0 opacity-50" />
        </Button>
      </PopoverTrigger>
      <PopoverContent class="p-0 w-[--radix-popper-anchor-width]">
        <Command v-model="value">
          <CommandInput placeholder="Search option..." />
          <CommandEmpty>No option found.</CommandEmpty>
          <CommandList>
            <CommandGroup>
              <CommandItem
                v-for="option in options"
                :key="option.value"
                :value="option.label"
                @select="open = false"
              >
                <Check
                  :class="
                    cn(
                      'mr-2 h-4 w-4',
                      value === option.label ? 'opacity-100' : 'opacity-0',
                    )
                  "
                />
                {{ option.label }}
              </CommandItem>
            </CommandGroup>
          </CommandList>
        </Command>
      </PopoverContent>
    </Popover>
  </div>
</template>

<style scoped></style>
