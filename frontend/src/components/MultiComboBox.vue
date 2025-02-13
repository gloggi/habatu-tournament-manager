<script setup lang="ts">
import { CommandEmpty, CommandGroup, CommandItem, CommandList } from '@/components/ui/command'
import {
  TagsInput,
  TagsInputInput,
  TagsInputItem,
  TagsInputItemDelete,
  TagsInputItemText,
} from '@/components/ui/tags-input'
import {
  ComboboxAnchor,
  ComboboxContent,
  ComboboxInput,
  ComboboxPortal,
  ComboboxRoot,
} from 'radix-vue'
import { computed, ref, onMounted } from 'vue'
import { useApi } from '@/api'
import { Label } from '@/components/ui/label'
const props = defineProps<{
  label: string
  optionsEntity: string
  valueKey: string
  modelValue: { id: number; label: string }[]
}>()

const emit = defineEmits(['update:modelValue'])

const api = useApi(props.optionsEntity)

onMounted(() => {
  api.fetchData()
})

const options = computed(() => {
  const { dataList } = api
  return dataList.value.map((item: any) => ({
    id: item.id,
    label: item.nickname,
  }))
})

const open = ref(false)
const searchTerm = ref('')

const value = computed<{ id: number; label: string }[]>({
  get: () => {
    // Ensure all values are strings
    return props.modelValue
  },
  set: (newValue) => {
    emit('update:modelValue', newValue)
  },
})

const filteredOptions = computed(() =>
  options.value.filter((i) => !value.value.map((v) => v.id).includes(i.id))
)
const removeItem = (id: number) => {
  value.value = value.value.filter((v) => v.id !== id)
}
</script>



<template>
  <Label for="multicombobox">{{ props.label }}</Label>
  <TagsInput class="px-0 gap-0" :model-value="value">
    <div class="flex flex-row gap-2 items-center px-3">
      <TagsInputItem v-for="item in value" :key="item.id" :value="item.label">
        <TagsInputItemText />
        <TagsInputItemDelete @click="removeItem(item.id)" />
      </TagsInputItem>
    </div>

    <ComboboxRoot v-model="value" v-model:open="open" v-model:search-term="searchTerm" class="w-full">
      <ComboboxAnchor as-child>
        <ComboboxInput :placeholder="props.label" as-child>
          <TagsInputInput class="w-full px-3" :class="value.length > 0 ? 'mt-2' : ''" @keydown.enter.prevent />
        </ComboboxInput>
      </ComboboxAnchor>

      <ComboboxPortal>
        <ComboboxContent>
          <CommandList
            position="popper"
            class="w-[--radix-popper-anchor-width] rounded-md mt-2 border bg-popover text-popover-foreground shadow-md outline-none data-[state=open]:animate-in data-[state=closed]:animate-out data-[state=closed]:fade-out-0 data-[state=open]:fade-in-0 data-[state=closed]:zoom-out-95 data-[state=open]:zoom-in-95 data-[side=bottom]:slide-in-from-top-2 data-[side=left]:slide-in-from-right-2 data-[side=right]:slide-in-from-left-2 data-[side=top]:slide-in-from-bottom-2"
            style="z-index: 50;"
          >
            <CommandEmpty />
            <CommandGroup>
              <CommandItem
                v-for="item in filteredOptions" :key="item.id" :value="item.label"
                
                @select.prevent="(ev) => {        
                  if (typeof ev.detail.value === 'string') {
                    searchTerm = ''
                    value = [...value, { id: item.id, label: item.label }]
                    open = false
                  }

                  if (filteredOptions.length === 0) {
                    open = false
                  }
                }"
              >
                {{ item.label }}
              </CommandItem>
            </CommandGroup>
          </CommandList>
        </ComboboxContent>
      </ComboboxPortal>
    </ComboboxRoot>
  </TagsInput>
</template>