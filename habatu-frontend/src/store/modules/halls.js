import { mixin } from "@/mixins"

export const halls = {
	namespaced: true,
	state() {
		return {
			halls: undefined,
		}
	},
	mutations: {
		saveHalls(state, halls) {
			state.halls = halls
		},
	},
	actions: {
		async create({ dispatch }, item) {
			try {
				await mixin.methods.callApi("post", "/halls", item)
				dispatch("get")
			} catch (e) {
				console.log(e)
			}
		},
		async get({ commit }) {
			try {
				const halls = await mixin.methods.callApi("get", "/halls")
				commit("saveHalls", halls.data)
			} catch (e) {
				console.log(e)
			}
		},
		async delete({ dispatch }, id) {
			try {
				await mixin.methods.callApi("delete", `/halls/${id}`)
				dispatch("get")
			} catch (e) {
				console.log(e)
			}
		},
		async update({ dispatch, commit }, hall) {
			try {
				await mixin.methods.callApi("put", `/halls/${hall._id}`, hall)
				commit(
					"notifications/showNotification",
					{ message: "Halls got updated!", type: true },
					{
						root: true,
					}
				)
				dispatch("get")
			} catch (e) {
				console.log(e)
			}
		},
	},
	getters: {},
}
