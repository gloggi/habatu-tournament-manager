import axios from "axios"
const api = axios.create({
	baseURL: `https://${process.env.VUE_APP_BACKEND_HOST}`,
	headers: {
		accept: "application/json",
	},
	timeout: 1000,
})
export const mixin = {
	methods: {
		async callApi(method, url, data) {
			try {
				const response = await api({
					method,
					url,
					data,
					headers: {
						authorization: `Bearer: ${localStorage.getItem("token")}`,
					},
				})
				return response
			} catch (e) {
				return JSON.stringify(e, Object.getOwnPropertyNames(e))
			}
		},
		userIsAdmin() {
			return this.$store.state.user.user.roles.includes("Admin")
		},
		userIsReferee() {
			return (
				this.$store.state.user.user.roles.includes("Admin") ||
				this.$store.state.user.user.roles.includes("Referee")
			)
		},
		userTeam() {
			return this.$store.state.user.user.team
		},
	},
}
