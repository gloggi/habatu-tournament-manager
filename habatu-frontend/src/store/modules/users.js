import { mixin } from "@/mixins"

export const users = {
	namespaced: true,
	state() {
		return {
			users: undefined,
		}
	},
	mutations: {
		saveUsers(state, users) {
			state.users = users
		},
	},
	actions: {
		async create({ dispatch }, item) {
			try {
				await mixin.methods.callApi("post", "/users", item)
				dispatch("get")
			} catch (e) {
				console.log(e)
			}
		},
		async get({ commit }) {
			try {
				const users = await mixin.methods.callApi("get", "/users")
				commit("saveUsers", users.data)
			} catch (e) {
				console.log(e)
			}
		},
		async delete({ dispatch }, id) {
			try {
				await mixin.methods.callApi("delete", `/users/${id}`)
				dispatch("get")
			} catch (e) {
				console.log(e)
			}
		},
		async update({ dispatch, commit }, user) {
			try {
				await mixin.methods.callApi("put", `/users/${user._id}`, user)
				commit(
					"notifications/showNotification",
					{ message: "User got updated!", type: true },
					{
						root: true,
					}
				)
				console.log(this)
				dispatch("get")
			} catch (e) {
				console.log(e)
			}
		},
	},
	getters: {},
}
