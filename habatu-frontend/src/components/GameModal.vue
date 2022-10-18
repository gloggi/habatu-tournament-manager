<template>
	<GenericModal
		v-if="isOpen"
		:close="close"
		title="Spiel bearbeite">
		<form @submit.prevent="updateGame">
			<JsonForm
				@changeForm="handleMainFormChange"
				:form="form"
				:values="game" />
			<BasicButton class="mt-2">Update</BasicButton>
		</form>
	</GenericModal>
</template>

<script>
import GenericModal from "./GenericModal.vue"
import JsonForm from "./JsonForm.vue"
import BasicButton from "./BasicButton.vue"
export default {
	components: { GenericModal, JsonForm, BasicButton },
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
