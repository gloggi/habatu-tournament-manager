<script setup lang="ts">
import { Trash2Icon } from 'lucide-vue-next'
import {
  Table,
  TableBody,
  TableCell,
  TableHead,
  TableHeader,
  TableRow,
} from '@/components/ui/table'

defineProps<{
  columnKeys: string[],
  columnNames: string[],
  rows: any[],
  deleteItem?: (id: string) => void
}>()

const nestedKeyToValue = (obj: any, key: string) => {
  return key.split('.').reduce((o, i) => o[i], obj)
}
</script>

<template>
  <Table>
    <TableHeader>
      <TableRow>
        <TableHead v-for="columnName in columnNames" class="w-full" :key="columnName">
          {{ columnName }}
        </TableHead>
        <TableHead v-if="deleteItem" class="w-fit">Aktionen</TableHead>
      </TableRow>
    </TableHeader>
    <TableBody>
      <TableRow v-for="row in rows" :key="row.id">
        <TableCell v-for="column in columnKeys" :key="column">
          {{ nestedKeyToValue(row, column) }}
        </TableCell>
        <TableCell class="flex justify-center text-muted-foreground " v-if="deleteItem">
          <button @click="deleteItem(row.id)"><component :is="Trash2Icon" /></button>
        </TableCell>
      </TableRow>
    </TableBody>
  </Table>
</template>