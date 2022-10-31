<template>
	<div class=" p-3">

		<div class="flex flex-col items-stretch h-full">
			<StepOverview :steps="steps" class="h-20" />
			<div class="flex flex-col h-full w-full rounded-md border bg-white text-left drop-shadow-lg">
				<div class="flex" style="height: 70vh">
					<form
				@submit.prevent="setOptions"
				:key="formKey"
				class="flex w-full flex-col space-y-3 border-r p-5">
				<h1 class="pb-3 text-2xl font-bold">Einstellungen</h1>
				<JsonForm
					v-if="items"
					@changeForm="handleMainFormChange"
					:form="form"
					:values="items" />

				<BasicButton>Set</BasicButton>
			</form>
					<div class="mb-10 w-full p-3">
						<div v-if="timePreview">
					<TitleItem class="mb-2">Game Facts</TitleItem>
					<h1 class="text-2xl font-medium">
						Anzahl Spiele:
						<span class="font-normal">{{ timePreview.amountOfGames }}</span>
					</h1>
					<h1 class="text-2xl font-medium">
						Letztes reguläres Spiel endet um:
						<span class="font-normal">{{
							formatDate(new Date(timePreview.lastGame), "HH:mm")
						}}</span>
					</h1>
				</div>
					</div>
				</div>
				<div class="flex justify-between">
					<router-link v-if="backRoute" :to="{ name: backRoute }"
			><BasicButton
				class=" rounded-tl-none rounded-br-none"
				>back</BasicButton
			></router-link
		>
		<router-link v-if="nextRoute" :to="{ name: nextRoute }"
			><BasicButton
				@click="$router.push({ to: nextRoute })"
				class="rounded-tr-none rounded-bl-none"
				>next</BasicButton
			></router-link
		></div>
			</div>
		</div>
	</div>
</template>

<script>
import BasicButton from "@/components/BasicButton.vue"
import JsonForm from "@/components/JsonForm.vue"
import { format } from "date-fns"
import TitleItem from "@/components/TitleItem.vue"
import StepOverview from '@/components/StepOverview.vue'
export default {
	components: { BasicButton, JsonForm, TitleItem, StepOverview },
	data() {
		return {
			item: {
				startTime: "09:00",
				gameDuration: "00:10",
				breakDuration: "00:05",
			},
			formKey: 0,
			toUpdateItems: {},
			backRoute: "teams",
			nextRoute: "menu",
			timePreview: undefined,
			form: [
				[
					{
						label: "Startzeit",
						model: "startTime",
						component: "TextField",
						type: "time",
					},
				],
				[
					{
						label: "Spielzeit",
						model: "gameDuration",
						component: "TextField",
						type: "time",
					},
				],
				[
					{
						label: "Pausenzeit",
						model: "breakDuration",
						component: "TextField",
						type: "time",
					},
				],
			],
			steps: [
				{ route: "halls", name: "Hallen" },
				{ route: "sections", name: "Abteilungen" },
				{ route: "categories", name: "Kategorien" },
				{ route: "teams", name: "Teams" },
				{ route: "options", name: "Einstellungen" },
			]
		}
	},
	computed: {
		items() {
			return this.$store.state.options.options
		},
	},
	methods: {
		formatDate(date, f) {
			return format(date, f)
		},
		async setOptions() {
			await new Promise(res => setTimeout(() => res(), 200))
			this.item.startedTournament=true
			this.$store.dispatch(`options/update`, this.item)
			this.getTimePreview()
			this.formKey++
		},
		handleMainFormChange(item) {
			this.item = item
		},
		async getTimePreview() {
			const response = await this.callApi("get", "/tournament/time-preview")
			this.timePreview = response.data
		},
	},
	created() {
		this.getTimePreview()
	},
}
</script>

<style></style>
