import { defineStore } from "pinia";

export const useTournamentTableStore = defineStore("tournamentTable", {
  state: () => ({
    showRefereeView: false,
    showPlayedView: false,
  }),
  actions: {
    toggleRefereeView() {
      this.showRefereeView = !this.showRefereeView;
    },
    togglePlayedView() {
      this.showPlayedView = !this.showPlayedView;
    },
    setShowRefereeView(value: boolean) {
      this.showRefereeView = value;
    },
    setShowPlayedView(value: boolean) {
      this.showPlayedView = value;
    },
  },
});
