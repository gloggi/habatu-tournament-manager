<template>
	<div
		v-if="rank"
		class="flex w-full flex-col space-y-2 p-3 md:flex-row md:space-x-2 md:space-y-0">
		<div
			class="flex w-full flex-col rounded-md border bg-white p-3 drop-shadow-lg"
			v-for="(category, i) in Object.keys(rank)"
			:key="i">
			<div class="flex font-bold">
				<div class="w-1/2">{{ category }}</div>
				<div class="w-1/4">Abteilung</div>
				<div class="w-1/4 text-end">P:GG:GK</div>
			</div>
			<div
				class="flex justify-between"
				v-for="(team, k) in rank[category]"
				:key="team._id">
				<div class="w-1/2">{{ k + 1 }}. {{ team.name }}</div>
				<div class="w-1/4" v-if="team.section">{{ team.section.name }}</div>
				<div class="w-1/4 text-end">
					{{ team.tournamentPoints }}:{{ team.pointsPro }}:{{ team.pointsCon }}
				</div>
			</div>
		</div>
	</div>
</template>

<script>
export default {
	data() {
		return {
			rank: undefined,
		}
	},
	methods: {
		async getRanking() {
			const response = await this.callApi("get", "/tournament/ranking")
			this.rank = response.data
		},
	},
	computed: {},
	created() {
		this.getRanking()
	},
}
</script>

<style></style>
