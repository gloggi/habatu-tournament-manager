import { computed } from "vue";
import { useOptionsStore } from "@/stores/options";

export function useGameTableRefreshRate() {
  const optionsStore = useOptionsStore();

  const gameTableRefreshRate = computed({
    get: () => optionsStore.getLocalGameTableRefreshRate,
    set: (value: number) => optionsStore.setLocalGameTableRefreshRate(value),
  });

  return {
    gameTableRefreshRate,
  };
}
