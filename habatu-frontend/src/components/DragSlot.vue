<template>
	<div
		v-if="userIsAdmin()"
		dropzone="true"
		@dragenter="dragging = true"
		@dragleave="dragging = false"
		:class="`rounded-md ${dragging ? 'bg-gray-500' : ''} hover:bg-gray-200`"
		@drop="dropHandler"
		@dragover="evt => evt.preventDefault()">
		<slot></slot>
	</div>
	<div v-else><slot></slot></div>
</template>

<script>
export default {
	props: ["hall", "timeslot"],
	data() {
		return {
			dragging: false,
		}
	},
	methods: {
		async dropHandler(evt) {
			var gameId = evt.dataTransfer.getData("gameId")
			await this.$store.dispatch("games/update", {
				_id: gameId,
				timeslot: this.timeslot,
				hall: this.hall,
			})
			await this.$store.dispatch("tournament/getTable")
			this.dragging = false
		},
	},
}
</script>

<style></style>
