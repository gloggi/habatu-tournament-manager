<template>
  <div class="rounded-md size-full">
    <div class="size-full flex justify-center items-center">
      <p>{{ getFormatedTime() }}</p>
    </div>
  </div>
</template>
<script setup lang="ts">
const props = defineProps<{
  time: string;
}>();

const getFormatedTime = () => {
  const parts = props.time.split("_");
  return `${parts[0]}:${parts[1]} - ${parts[2]}:${parts[3]}`;
};
</script>
