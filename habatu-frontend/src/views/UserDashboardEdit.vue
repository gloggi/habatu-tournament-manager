<template>
	<div class="mb-4 rounded-md bg-white p-5">
		<TitleItem>User</TitleItem>
		<form @submit.prevent="updateUser" v-if="user">
			<JsonForm
				@changeForm="handleMainFormChange"
				:form="form"
				:values="user" />
			<BasicButton class="mt-2">Update</BasicButton>
		</form>
	</div>
</template>

<script>
import BasicButton from "@/components/BasicButton.vue"
import JsonForm from "@/components/JsonForm.vue"
import TitleItem from "@/components/TitleItem.vue"

export default {
	data() {
		return {
			user: undefined,
		}
	},
	methods: {
		async getUser() {
			try {
				const response = await this.callApi("get", `/users/${this.routeId}`)
				this.user = response.data
			} catch (e) {
				console.log(e)
			}
		},
		async updateUser() {
			await this.$store.dispatch(`users/update`, this.user)
			await this.getUser()
		},
	},
	computed: {
		routeId() {
			return this.$route.params.id
		},
		form() {
			return [
				[{ label: "Nickname", model: "nickname", component: "TextField" }],
				[{ label: "Rolle", model: "role", component: "TextField" }],
			]
		},
	},
	created() {
		this.getUser()
	},
	components: { JsonForm, BasicButton, TitleItem },
}
</script>

<style></style>
