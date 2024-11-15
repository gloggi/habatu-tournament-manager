<template>
	<div
		v-if="rank"
		class="flex w-full flex-col space-y-2 p-3">
		<div
			class="flex w-full flex-col rounded-md border bg-white p-3 drop-shadow-lg"
			v-for="(category, i) in Object.keys(rank)"
			:key="i">
			<div class="flex font-bold">
				<div class="w-1/2">{{ category }}</div>
				<div class="w-1/4">Abteilung</div>
				<div class="w-1/4 text-end">
					<div class="flex justify-between">
						<div class="w-1/6">P</div>
						<div class="w-1/12">:</div>
						<div class="w-1/6">GD</div>
						<div class="w-1/12">:</div>
						<div class="w-1/6">GG</div>
						<div class="w-1/12">:</div>
						<div class="w-1/6">GK</div>
						<div class="w-1/12">:</div>
						<div class="w-1/6">SG</div>
					</div>
					</div>
			</div>
			<div
				class="flex justify-between"
				v-for="(team, k) in rank[category]"
				:key="team._id">
				<div class="w-1/2">{{ k + 1 }}. {{ team.name }}</div>
				<div class="w-1/4" v-if="team.section">{{ team.section.name }}</div>
				<div class="w-1/4 text-end">
					<div class="flex justify-between">
						<div class="w-1/6">{{ team.tournamentPoints }}</div>
						<div class="w-1/12">:</div>
						<div class="w-1/6">{{ team.pointsDifference }}</div>
						<div class="w-1/12">:</div>
						<div class="w-1/6">{{ team.pointsPro }}</div>
						<div class="w-1/12">:</div>
						<div class="w-1/6">{{ team.pointsCon }}</div>
						<div class="w-1/12">:</div>
						<div class="w-1/6">{{ team.gamesPlayed }}</div>
					</div>
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
		setInterval(()=>{this.getRanking()}, 5000)
	},
}
</script>

<style></style>
