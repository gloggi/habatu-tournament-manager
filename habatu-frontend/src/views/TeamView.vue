<template>
	<div class="justify-stretch flex h-screen flex-col items-center">
		<div class="mb-2 w-full rounded-md bg-white p-3 drop-shadow-lg md:w-1/3">
			<h1 class="text-3xl font-medium">{{ team.name }}</h1>
		</div>
		<div
			class="flex h-full w-full flex-col space-y-2 rounded-md bg-white p-3 drop-shadow-lg md:w-1/3">
			<template v-for="time in Object.keys(table)" :key="table[time]._id">
				{{ time }}
				<GameField :games="[table[time]]"></GameField>
			</template>
		</div>
	</div>
</template>

<script>
import GameField from "@/components/GameField.vue"
export default {
	data() {
		return {
			table: undefined,
		}
	},
	methods: {
		async getGroupTable() {
			const response = await this.callApi(
				"get",
				`/tournament/table/${this.team._id}`
			)
			this.table = response.data
		},
	},
	computed: {
		team() {
			return this.userTeam()
		},
	},
	created() {
		this.getGroupTable()
	},
	components: { GameField },
}
</script>

<style></style>
