<template>
	<div v-if="team" class="justify-stretch flex h-full flex-col items-center">
		<div class="mb-2 w-full rounded-md bg-white p-3 drop-shadow-lg md:w-1/3">
			<h1 class="text-3xl font-medium">{{ team.name }}</h1>
		</div>
		<div v-if="Object.keys(table).length > 0"
			class="flex flex-1 h-full w-full flex-col space-y-2 rounded-md bg-white p-3 drop-shadow-lg md:w-1/3">
			<template v-for="time in Object.keys(table)" :key="table[time]._id">
				{{ time }}
				<GameField :games="[table[time]]"></GameField>
			</template>
		</div>
	</div>
	<div v-else class="mb-2 w-full rounded-md bg-white p-3 drop-shadow-lg md:w-1/3">
			<h1 class="text-3xl font-medium pb-3">Tu hesch keis Team</h1>
			<router-link :to="{name: 'me'}"><BasicButton>Da chasch dis Team uswähle</BasicButton></router-link>
		</div>
</template>

<script>
import BasicButton from "@/components/BasicButton.vue";
import GameField from "@/components/GameField.vue"

export default {
	data() {
		return {
			table: {},
		}
	},
	methods: {
		async getGroupTable() {
			if(!this.team){
				return
			}
			try {
				const response = await this.callApi(
					"get",
					`/tournament/table/${this.team._id}`
				);
				this.table = response.data;
			} catch (error) {
				console.error("Error fetching group table:", error);
				// Handle the error appropriately
			}
		},
	},
	computed: {
		team() {
			return this.userTeam()
		},
	},
	watch: {
		team(newValue) {
			if (newValue) {
				this.getGroupTable();
			}
		}
	},
	created(){
		this.getGroupTable();

	},
	components: { GameField, BasicButton },
}
</script>

<style></style>
