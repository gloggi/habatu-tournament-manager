<template>
	<div
		class="-m-2 flex h-screen flex-col-reverse justify-center md:w-full md:flex-row md:items-stretch">
		<div
			class="flex h-full w-full flex-col bg-white pt-5 md:w-1/3 md:justify-center md:pt-0">
			<p class="mb-2 px-5 text-center text-4xl font-black">
				{{ login ? "Ihlogge" : "Registriere" }}
			</p>
			<LoginOrRegister @switch="handleSwitch" :login="login" />

			<form
				@submit.prevent="loginUser"
				v-if="login"
				class="space-y-2 px-5 text-left">
				<TextInput v-model="loginForm.nickname" label="Pfadiname" type="text" />
				<TextInput
					v-model="loginForm.password"
					label="Passwort"
					type="password" />
				<BasicButton>Ihlogge</BasicButton>
			</form>
			<form
				@submit.prevent="registerUser"
				v-if="!login"
				class="space-y-2 px-5 text-left">
				<TextInput
					v-model="registerForm.nickname"
					label="Pfadiname"
					type="text" />
				<SelectField label="Nie ohne mein Team, wo heisst ..." v-model="registerForm.team" :options="this.$store.state.teams.teams" />
				<TextInput
					v-model="registerForm.password"
					label="Passwort"
					type="password" />
				<BasicButton>Registriere</BasicButton>
			</form>
		</div>
		<div
			class="flex h-1/3 w-full flex-col items-center justify-center space-y-2 bg-red-600 py-4 md:h-full md:w-2/3 md:space-y-8 md:py-0">
			<h1
				class="bg-white bg-clip-text px-5 text-center text-2xl font-bold text-transparent md:px-10 md:text-7xl md:font-black">
				HaBaTu Tournament Manager
			</h1>
			<img
				class="h-40 w-40 md:mt-10 md:h-72 md:w-72"
				src="@/assets/rotating_ball_a.png" />
		</div>
	</div>
</template>

<script>
import TextInput from "@/components/TextInput.vue"
import BasicButton from "@/components/BasicButton.vue"
import LoginOrRegister from "@/components/LoginOrRegister.vue"
import SelectField from "@/components/SelectField.vue"
export default {
	components: { TextInput, BasicButton, LoginOrRegister, SelectField },
	data() {
		return {
			login: true,
			registerForm: {
				nickname: undefined,
				password: undefined,
				team: undefined
			},
			loginForm: {
				nickname: undefined,
				password: undefined,
			},
		}
	},
	methods: {
		sendError(msg) {
			this.$store.commit("notifications/showNotification", {
				message: msg,
				type: false,
			})
		},
		handleSwitch(evt) {
			this.login = evt
		},
		async loginUser() {
			if (!this.loginForm.nickname) {
				this.sendError("De Pfadiname chan nöd leer si!")
				return
			}
			if (!this.loginForm.password) {
				this.sendError("Du hesch keis Passwort ihgeh")
				return
			}

			await this.$store.dispatch("user/login", this.loginForm)
			if (localStorage.token) {
				this.$router.push({ name: "menu" })
			}
		},
		async registerUser() {
			if (!this.registerForm.nickname) {
				this.sendError("De Pfadiname chan nöd leer si!")
				return
			}
			if (!this.registerForm.password) {
				this.sendError("Du hesch keis Passwort ihgeh")
				return
			}
			await this.$store.dispatch("user/register", this.registerForm)
			if (localStorage.token) {
				this.$router.push({ name: "menu" })
			}
		},
	},
}
</script>

<style></style>
