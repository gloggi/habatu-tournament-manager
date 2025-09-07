import { defineStore } from "pinia";
import { useApi } from "@/api";
import { User } from "@/types";
import { useRouter } from "vue-router";

export const useUserStore = defineStore("user", {
  state: () => ({
    user: undefined as User | undefined,
    router: useRouter(),
  }),

  getters: {
    getUserId: (state) => state.user?.id,
    isAdmin: (state) => state.user?.role === "admin",
    isReferee: (state) =>
      state.user?.role === "referee" || state.user?.role === "admin",
    getUserNickname: (state) => state.user?.nickname ?? "",
    getTeamName: (state) => state.user?.team?.name ?? "",
    getSectionName: (state) => state.user?.section?.name ?? "",
  },

  actions: {
    async fetchUser() {
      if (this.user) return;

      const { fetchData, data } = useApi<User>("auth");
      try {
        await fetchData(undefined, true);
        this.user = data.value || undefined;
      } catch (e) {
        this.user = undefined;
        localStorage.removeItem("token");
        this.router.push("/login");
      }
    },

    setUser(userData: User) {
      this.user = userData;
    },

    clearUser() {
      this.user = undefined;
    },
    logout() {
      localStorage.removeItem("token");
      this.router.push("/login");
      this.clearUser();
    },
  },
});
