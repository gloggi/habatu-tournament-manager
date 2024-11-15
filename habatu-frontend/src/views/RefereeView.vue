<template>
	<div class="justify-stretch flex h-full flex-col items-center">
		<div class="mb-2 w-full rounded-md bg-white p-3 drop-shadow-lg md:w-1/3">
			<h1 class="text-3xl font-medium">Ich bin Schiri vo:</h1>
		</div>
		<div
			class="flex h-full w-full flex-col space-y-2 rounded-md bg-white p-3 text-center drop-shadow-lg md:w-1/3">
			<template v-for="game in myGames" :key="game._id">
				{{ formatTimeslot(game.timeslot) }}
				<router-link
					:to="{ name: 'gameRefereeingView', params: { id: game._id } }">
					<GameField :games="[game]"></GameField>
				</router-link>
			</template>
		</div>
	</div>
</template>
<script>
import GameField from "@/components/GameField.vue"
import { format } from "date-fns"

export default {
	computed: {
		myGames() {
			return (this.$store.state.user.user?.refereeGames || []).filter(game => game);
		},
	},
	methods: {
		formatTimeslot(timeslot) {
			return `${format(new Date(timeslot.startTime), "HH:mm")} - ${format(
				new Date(timeslot.endTime),
				"HH:mm"
			)}`
		},
	},
	components: { GameField },
	async created() {
		await this.$store.dispatch("user/getMe", localStorage.token)
	},
}
</script>
