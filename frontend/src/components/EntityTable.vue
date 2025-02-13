<script setup lang="ts">
import {
  Table,
  TableBody,
  TableCell,
  TableHead,
  TableHeader,
  TableRow,
} from '@/components/ui/table'
import H1 from '@/components/H1.vue'
import { ref, watch, computed } from 'vue'
import { useApi } from '@/api'

import { Button } from '@/components/ui/button'
import { Plus } from 'lucide-vue-next'
import { useRouter } from 'vue-router'

const router = useRouter();

const props = defineProps<{
  entity: string;
  name: string;
  columnKeys: string[];
  columnNames: string[];
}>();

// Create a ref to store the useApi result
let api = ref(useApi(props.entity));

// Function to initialize useApi with the correct entity
const initializeApi = () => {
  api.value = useApi(props.entity);
};

const dataList = computed<any[]>(() => {
  const { dataList } = api.value;
  return dataList;
});

const navigateToNewItem = () => {
  router.push(`${props.entity}/new`);
};

watch(
  () => props.entity,
  () => {
    initializeApi(); // Reinitialize the API
    api.value.fetchData(); // Fetch data for the new entity
  },
  { immediate: true }
); // immediate ensures watcher triggers on mount

</script>

<template>
<H1 class="mb-4">{{ props.name }}</H1>
<div class="w-full flex justify-end space-x-2">
      <Button variant="outline" size="icon" @click="navigateToNewItem">
        <Plus class="w-4 h-4" />
      </Button>
    </div>
  <Table>
    <TableHeader>
      <TableRow>
        <TableHead v-for="column in props.columnNames" >
          {{ column }}
        </TableHead>
      </TableRow>
    </TableHeader>
    <TableBody>
      <TableRow v-for="(data, i) in dataList">
        <TableCell v-for="(key, j) in props.columnKeys">
          <router-link class="font-semibold" v-if="j === 0" :to="`${props.entity}/${data.id}`">
          {{ data[key] }}
          </router-link>
        </TableCell>
      </TableRow>
    </TableBody>
  </Table>
</template>