import { mixin } from "@/mixins"

export const user = {
	namespaced: true,
	state() {
		return {
			user: {nickname: undefined},
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
				console.log(response.data)
				const user = response.data
				if (user.token) {
					commit("setUser", user)
				}
			} catch (e) {
				console.log(e)
			}
		},
	},
	getters: {},
}
