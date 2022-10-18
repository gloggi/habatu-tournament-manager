import axios from "axios"
const api = axios.create({
	baseURL: "http://localhost:8000",
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
					headers: { token: localStorage.getItem("token") },
				})
				return response
			} catch (e) {
				return JSON.stringify(e, Object.getOwnPropertyNames(e))
			}
		},
	},
}
