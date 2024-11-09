<template>
	<GenericModal v-if="isOpen" :close="close" title="Spiel bearbeite">
		<div class="flex-flex-col space-y-4">
			<form @submit.prevent="updateGame">
				<JsonForm
					@changeForm="handleMainFormChange"
					:form="form"
					:values="game" />
				<BasicButton class="mt-2">Update</BasicButton>
			</form>
			<div class="flex justify-end" v-if="game.type != 'roundGame'">
				<button @click="deleteGame"><TrashIcon /></button>
			</div>
			<div class="flex justify-center">
				<div class="w-1/4">
					<CollapseItem title="Schiri Code">
						<div class="flex justify-center">
							<QrcodeVue :value="gameLink" :size="150" />
						</div>
						<p><a :href="gameLink">{{ gameLink }}</a></p>
					</CollapseItem>
				</div>
			</div>
			<div class="flex justify-center">
				<div class="w-1/4">
					<h3 class="text-xl">Schiris</h3>
					<div v-for="referee in game.referees" :key="referee._id">
							<p>{{ referee.nickname }}</p>
						</div>
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
import TrashIcon from "./icons/TrashIcon.vue"
export default {
	components: {
		GenericModal,
		JsonForm,
		BasicButton,
		CollapseItem,
		QrcodeVue,
		TrashIcon,
	},
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
			this.$store.dispatch("tournament/getTable")
			this.close()
		},
		deleteGame() {
			this.$store.dispatch(`games/delete`, this.game._id)
			this.$store.dispatch("tournament/getTable")
			this.close()
		},
	},
	computed: {
		isOpen() {
			return this.$store.state.gameModal.open
		},
		game() {
			return { ...this.$store.state.gameModal.game }
		},
		gameLink() {
			return `${window.location.protocol + "//" + window.location.host}/referee/add/${this.$store.state.gameModal.game._id}`
		},
		form() {
			return [
				[
					{
						model: "teamA",
						class: "w-full",
					},
					{
						model: "teamB",
						class: "w-full",
					},
				],
				[
					{
						label: `Pünkt Team A`,
						model: "pointsTeamA",
						component: "TextField",
					},
					{
						label: `Pünkt Team B`,
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
