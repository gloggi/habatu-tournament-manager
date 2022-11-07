<template>
	<div class="mb-4 rounded-md bg-white p-5">
		<TitleItem>User</TitleItem>
		<form @submit.prevent="updateUser" v-if="user">
			<JsonForm
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
				if(response.data.role){
					response.data.role = {_id: response.data.role, name: response.data.role}
				}
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
				[{ label: "Pfadiname", model: "nickname", component: "TextField" }],
				[{ label: "Neus Passwort setze", model: "password", component: "TextField", type:"password" }],
				[{ label: "Rolle", model: "role", component: "SelectField", 
				options:[
					{_id:"Admin",name:"Admin"},
					{_id:"Referee",name:"Referee"},
					{_id:"Teammember",name:"Teammember"},
				] }],
				[{ label: "Team", model: "team", component: "SelectField", 
				options: this.$store.state.teams.teams}],
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
