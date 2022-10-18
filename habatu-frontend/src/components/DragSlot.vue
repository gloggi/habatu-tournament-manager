<template>
	<div
		dropzone="true"
		@dragenter="dragging = true"
		@dragleave="dragging = false"
		:class="`rounded-md ${dragging ? 'bg-red-500' : ''} hover:bg-red-300`"
		@drop="dropHandler"
		@dragover="evt => evt.preventDefault()">
		<slot></slot>
	</div>
</template>

<script>
export default {
	props: ["hall", "timeslot"],
	emits: ["reload"],
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
			this.$emit("reload", true)
			this.dragging = false
		},
	},
}
</script>

<style></style>
