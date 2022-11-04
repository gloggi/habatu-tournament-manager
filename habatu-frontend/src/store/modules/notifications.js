export const notifications = {
	namespaced: true,
	state() {
		return {
			show: false,
			message: undefined,
			type: undefined,
		}
	},
	mutations: {
		showNotification(state, notification) {
			state.show = true
			state.message = notification.message
			state.type = notification.type
			setTimeout(() => {
				state.show = false
				state.message = undefined
			}, 2000)
		},
	},
	actions: {},
	getters: {},
}
