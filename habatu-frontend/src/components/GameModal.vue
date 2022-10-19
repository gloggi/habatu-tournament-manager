<template>
	<GenericModal
		v-if="isOpen"
		:close="close"
		title="Spiel bearbeite">
		<div class="flex-flex-col space-y-4">
		<form @submit.prevent="updateGame">
			<JsonForm
				@changeForm="handleMainFormChange"
				:form="form"
				:values="game" />
			<BasicButton class="mt-2">Update</BasicButton>
		</form>
		<div class="flex justify-center">
			<div class="w-1/4">
			<CollapseItem title="Referee Code">
				<div class="flex justify-center">
				<QrcodeVue :value="gameLink" :size="150" />
			</div>
			
			</CollapseItem>
		</div>
		</div>
	</div>

	</GenericModal>
</template>

<script>
import GenericModal from "./GenericModal.vue"
import JsonForm from "./JsonForm.vue"
import BasicButton from "./BasicButton.vue"
import CollapseItem from "./CollapseItem.vue"
import QrcodeVue from "qrcode.vue"
export default {
	components: { GenericModal, JsonForm, BasicButton, CollapseItem, QrcodeVue },
	data() {
		return {
			item: undefined,
		}
	},
	methods: {
		close() {
			this.$store.commit("gameModal/closeModal")
		},
		handleMainFormChange(item) {
			this.item = item
		},
		updateGame() {
			this.$store.dispatch(`games/update`, this.item)
		},
	},
	computed: {
		isOpen() {
			return this.$store.state.gameModal.open
		},
		game() {
			return { ...this.$store.state.gameModal.game }
		},
		gameLink(){
			return `http://${process.env.VUE_APP_FRONTEND_HOST}/referee/${this.$store.state.gameModal.game._id}`
		},
		form() {
			return [
				[
					{
						model: "teamA",
						class: "w-full"
					},
					{
						model: "teamB",
						class: "w-full"
					},
				],
				[
					{
						label: `Punkte Team A`,
						model: "pointsTeamA",
						component: "TextField",
					},
					{
						label: `Punkte Team B`,
						model: "pointsTeamB",
						component: "TextField",
					},
				],
			]
		},
	},
}
</script>

<style></style>
