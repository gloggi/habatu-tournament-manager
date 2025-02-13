<template>
  <div class="flex space-x-5 h-3/4">
    <div class="w-full">
      <HeadingOne>Neue {{ name }} erstellen</HeadingOne>
      <form @submit.prevent="createHall" class="flex flex-col space-y-5">
        <template v-for="field in props.form" :key="field.key">
          <ComboBox
            v-if="field.type == 'select' && field.optionsEntity"
            :label="field.label"
            :optionsEntity="field.optionsEntity"
            v-model="newEntity[field.key]"
          />
          <InputField
            v-else
            :label="field.label"
            :type="field.type"
            :required="field.required"
            v-model="newEntity[field.key]"
          />
        </template>
        <Button variant="outline">speichern</Button>
      </form>
    </div>
    <div class="w-full mb-10">
      <HeadingOne>Erfasste {{ name }}</HeadingOne>
      <div class="h-full overflow-scroll">
        <CreationTable
          :columnKeys="columnKeys"
          :columnNames="columnNames"
          :rows="[...dataList.values()]"
          :deleteItem="deleteHall"
        />
      </div>
    </div>
  </div>
</template>
<script setup lang="ts">
import CreationTable from "@/components/CreationTable.vue";
import HeadingOne from "@/components/HeadingOne.vue";
import InputField from "@/components/InputField.vue";
import { Button } from "@/components/ui/button";
import { ref, onMounted, watch, computed } from "vue";
import { useApi } from "@/api";
import { IForm } from "@/types";
import ComboBox from "./ComboBox.vue";
import { useToast } from '@/components/ui/toast/use-toast'
const { toast } = useToast()

const currentStep = defineModel("currentStep", {
  type: Number,
  default: 1,
});

const props = defineProps<{
  entity: string;
  name: string;
  columnKeys: string[];
  columnNames: string[];
  form: IForm[];
}>();

// Create a ref to store the useApi result
let api = ref(useApi(props.entity));

// Function to initialize useApi with the correct entity
const initializeApi = () => {
  api.value = useApi(props.entity);
};

const dataList = computed(() => {
  const { dataList } = api.value;
  return dataList;
});

// Watch for changes in the entity prop to reinitialize useApi and fetch the updated data
watch(
  () => props.entity,
  () => {
    initializeApi(); // Reinitialize the API
    api.value.fetchData(); // Fetch data for the new entity
  },
  { immediate: true }
); // immediate ensures watcher triggers on mount

const newEntity = ref<Record<string, number | string>>({});

// Fetch data when the component is mounted
onMounted(() => {
  api.value.fetchData();
});

// Create a new entity and fetch the updated data list
const createHall = async () => {
  try {
    await api.value.createData(newEntity.value);
  } catch (error) {
    toast({
      title: 'Es ist ein Fehler aufgetreten',
      variant: 'destructive',
    })
    console.log(error);
  }
  api.value.fetchData();
  newEntity.value = {};
};

// Delete an entity and fetch the updated data list
const deleteHall = async (id: string) => {
  await api.value.deleteData(id);
  api.value.fetchData();
};
</script>
