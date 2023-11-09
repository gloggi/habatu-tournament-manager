<template>
	<div
		class="2xl:text-md flex w-full cursor-pointer justify-center space-x-1 rounded-md border p-2 text-center font-normal 2xl:p-1"
		@click="
			() => {
				if (userIsAdmin()) {
					openGameModal(game)
				}
			}
		"
		draggable="true"
		@dragstart="evt => dragStartHandler(evt, game._id)"
		v-for="(game, i) in games"
		:style="`background: ${game.category.color}; ${
			game.type == 'grandFinale'
				? 'border-width:2px;border-color: #ffd700;'
				: ''
		} ${
			game.type == 'petiteFinale'
				? 'border-width:2px;border-color: #bf8970;'
				: ''
		}`"
		:key="i">
		<div class="w-1/2 text-right">{{ game.teamA.name }}</div>
		<div class="w-1/6">{{ game.pointsTeamA }} : {{ game.pointsTeamB }}</div>
		<div class="w-1/2 text-left">{{ game.teamB.name }}</div>
	</div>
	
	<div v-if="games.length == 0" class="p-2">&nbsp;</div>
</template>

<script>
export default {
	props: ["games"],
	methods: {
		dragStartHandler(evt, gameId) {
			evt.dataTransfer.setData("gameId", gameId)
		},
		openGameModal(game) {
			this.$store.commit("gameModal/openModal", game)
		},
	},
}
</script>

<style></style>
