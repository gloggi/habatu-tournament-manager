import { mixin } from "@/mixins"

export const tournament = {
	namespaced: true,
	state() {
		return {
			table: undefined,
		}
	},
	mutations: {
		saveTournamentTable(state, table) {
			state.table = table
		},
	},
	actions: {
		async getTable({ commit }) {
			try {
				const tournamentTable = await mixin.methods.callApi(
					"get",
					"/tournament/table"
				)
				commit("saveTournamentTable", tournamentTable.data)
			} catch (e) {
				console.log(e)
			}
		},
	},
	getters: {},
}
