export const gameModal = {
	namespaced: true,
	state() {
		return {
			open: false,
			game: undefined,
		}
	},
	mutations: {
		openModal(state, game) {
			state.open = true
			state.game = game
		},
		closeModal(state) {
			state.open = false
			state.game = undefined
		},
	},
	actions: {},
	getters: {},
}
