import { mixin } from "@/mixins"

export const user = {
	namespaced: true,
	state() {
		return {
			user: { nickname: undefined, role: "", team: undefined },
		}
	},
	mutations: {
		setUser(state, user) {
			state.user = user
			if (user.token) {
				localStorage.setItem("token", user.token)
			}
		},
	},
	actions: {
		async login({ commit }, credentials) {
			try {
				const response = await mixin.methods.callApi(
					"post",
					"/users/login",
					credentials
				)
				console.log(response.data)
				const user = response.data
				if (user.token) {
					commit("setUser", user)
				}
			} catch (e) {
				console.log(e)
				commit(
					"notifications/showNotification",
					{ message: e.response.data.message, type: false },
					{
						root: true,
					}
				)
			}
		},
		async register({ commit }, credentials) {
			try {
				const response = await mixin.methods.callApi(
					"post",
					"/users",
					credentials
				)
				const user = response.data
				if (user) {
					commit("setUser", user)
				}
			} catch (e) {
				console.log(e)
				commit(
					"notifications/showNotification",
					{ message: e.response.data.message, type: false },
					{
						root: true,
					}
				)
			}
		},
		async getMe({ commit }, token) {
			if (!token) {
				return
			}
			try {
				const response = await mixin.methods.callApi("post", "/users/me", {
					token,
				})
				console.log(response.status)
				console.log(response.data)
				const user = response.data
				if (user.token) {
					commit("setUser", user)
				}
			} catch (e) {
				localStorage.removeItem("token")
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
				await dispatch("getMe", user.token)
			} catch (e) {
				console.log(e)
			}
		},
	},
	getters: {},
}
