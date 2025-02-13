<script setup lang="ts">
import { computed, onMounted } from 'vue';
import { useRoute, useRouter } from 'vue-router';
import { useToast } from '@/components/ui/toast/use-toast';
import { useApi } from '@/api';

import H1 from '@/components/H1.vue';
import Container from '@/components/Container.vue';
import { Button } from '@/components/ui/button';
import { Save, Trash } from 'lucide-vue-next';

const props = defineProps<{
  resourceName: string;
}>();

const route = useRoute();
const router = useRouter();
const { toast } = useToast();
const {
  fetchData,
  data: item,
  updateData,
  createData,
  deleteData,
} = useApi<any>(props.resourceName);

const id = computed(() => {
  const idParam = route.params.id as string;
  const parsedId = parseInt(idParam);
  return isNaN(parsedId) ? undefined : parsedId;
});

onMounted(() => {
  if (id.value === undefined) {
    item.value = {};
  } else {
    fetchData(id.value).catch((error) => {
      console.error(error);
      toast({ title: `Error loading ${props.resourceName}`, variant: 'destructive' });
    });
  }
});

const title = computed(() => item.value?.name || `New ${props.resourceName}`);

const updateOrCreateItem = async () => {
  if (!item.value) return;

  try {
    if (!item.value.id) {
      await createData(item.value);
      toast({ title: `${props.resourceName} created` });
    } else {
      await updateData(item.value.id, item.value);
      toast({ title: `${props.resourceName} updated` });
    }
    router.push(`/admin/${props.resourceName}`);
  } catch (error) {
    console.error(error);
    toast({ title: 'Error saving', variant: 'destructive' });
  }
};

const deleteItem = async () => {
  if (!item.value?.id) return;

  try {
    await deleteData(item.value.id);
    toast({ title: `${props.resourceName} deleted` });
    router.push(`/admin/${props.resourceName}`);
  } catch (error) {
    console.error(error);
    toast({ title: 'Error deleting', variant: 'destructive' });
  }
};
</script>

<template>
  <Container class="w-full">
    <H1>{{ title }}</H1>
    <div class="w-full flex justify-end space-x-2">
      <Button variant="outline" size="icon" @click="deleteItem" :disabled="!item?.id">
        <Trash class="w-4 h-4" />
      </Button>
      <Button variant="outline" size="icon" @click="updateOrCreateItem">
        <Save class="w-4 h-4" />
      </Button>
    </div>
    <form v-if="item" @submit.prevent="updateOrCreateItem" class="flex flex-col space-y-2">
      <slot :item="item"></slot>
    </form>
  </Container>
</template>