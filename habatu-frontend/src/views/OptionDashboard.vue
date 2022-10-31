<template>
	<div class="flex h-full w-full flex-col items-stretch">
		<div class="mb-4 rounded-md bg-white p-3">
			<TitleItem>Options</TitleItem>
		</div>
		<div class="w-full rounded-md bg-white p-3" :key="optionsKey" v-if="options&&storedOptions">
			<h2 class="text-2xl font-medium">General Options</h2>
			<div class="flex flex-col space-y-2">
				<div class="flex">
					<TextInput label="Tournament Name" v-model="options.tournamentName" />
				</div>
				<h2 class="text-2xl font-medium">Game Specific Options</h2>
				<div class="flex">
					<SwitchItem label="Has the tournament started?" v-model="options.startedTournament" />
				</div>
				<div class="flex items-end justify between">
					<div class="w-1/4">
						<SwitchItem label="Are all round Games finished?" v-model="options.endedRoundGames" />
					</div>
					<div>
						<BasicButton :disabled="!storedOptions.endedRoundGames" @click="generateFinals">Generate Finals</BasicButton>
					</div>
				</div>
				<div class="flex justify-end"><BasicButton class="" @click="updateOptions">Save</BasicButton></div>
			</div>
		</div>
	</div>
</template>

<script>
import TitleItem from "@/components/TitleItem.vue"
import TextInput from "@/components/TextInput.vue";
import SwitchItem from "@/components/SwitchItem.vue";
import BasicButton from "@/components/BasicButton.vue";
export default {
	components: { TitleItem, TextInput, SwitchItem, BasicButton },
	data() {
		return {
			options: undefined,
			optionsKey: 0
		}
	},
	computed: {
		storedOptions() {
			return this.$store.state.options.options
		}

	},
	methods: {
		async updateOptions() {
			await this.$store.dispatch(`options/update`, this.options)
			await this.$store.dispatch(`options/get`)
			this.cloneOptions()
			this.optionsKey++
		},
		cloneOptions() {
			this.options = { ...this.$store.state.options.options }
			this.optionsKey++
		},
		async generateFinals(){
			try{
				await this.callApi("get","/tournament/create-finals")
				this.$store.commit("notifications/showNotification", {message: "Finals were created!", type: true})

				
			}catch(e){
				console.log(e)
				
			}
		}
	},
	async created() {
		await this.$store.dispatch("options/get")
		this.cloneOptions()
		
	}
}
</script>

<style>

</style>
