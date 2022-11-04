<template>
	<div class="justify-stretch flex h-screen flex-col items-center" v-if="user">
		<div class="mb-2 w-full rounded-md bg-white p-3 drop-shadow-lg md:w-1/3">
			<h1 class="text-3xl font-medium">{{ user.nickname }}</h1>
		</div>
		<form
			@submit.prevent="update"
			class="flex h-full w-full flex-col space-y-2 rounded-md bg-white p-3 drop-shadow-lg md:w-1/3">
			<SelectField label="Mein Team" :options="teams" v-model="user.team" />
			<BasicButton>Update</BasicButton>
		</form>
	</div>
</template>

<script>
import BasicButton from "@/components/BasicButton.vue"
import SelectField from "@/components/SelectField.vue"

export default {
	data() {
		return {
			user: {},
		}
	},
	methods: {
		cloneUser() {
			this.user = { ...this.$store.state.user.user }
		},
		async update() {
			await this.$store.dispatch("user/update", this.user)
			await this.$store.dispatch("user/getMe", localStorage.getItem("token"))
			this.cloneUser()
		},
	},
	computed: {
		nickname() {
			return this.$store.state.user.user?.nickname
		},
		teams() {
			return this.$store.state.teams.teams
		},
	},
	components: { SelectField, BasicButton },
	created() {
		this.cloneUser()
	},
}
</script>

<style></style>
