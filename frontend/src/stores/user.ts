import { defineStore } from "pinia";
import { useApi } from "@/api";
import { User } from "@/types";

export const useUserStore = defineStore("user", {
  state: () => ({
    user: null as User | null,
  }),

  getters: {
    getUserId: (state) =>
      state.user?.id || parseInt(localStorage.getItem("userId") || "0"),
    isAdmin: (state) => state.user?.role === "admin",
    getUserNickname: (state) => state.user?.nickname ?? "",
    getTeamName: (state) => state.user?.team?.name ?? "",
    getSectionName: (state) => state.user?.section?.name ?? "",
  },

  actions: {
    async fetchUser() {
      const userId = localStorage.getItem("userId");
      if (!userId) return;
      if (this.user) return;

      const { fetchData, data } = useApi<User>("users");
      await fetchData(userId, true);
      this.user = data.value;
    },

    setUser(userData: User) {
      this.user = userData;
    },

    clearUser() {
      this.user = null;
    },
  },
});
