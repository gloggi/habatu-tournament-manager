<template>
	<div class="p-3">
		<div class="flex h-full flex-col items-stretch">
			<StepOverview :steps="steps" class="h-20" />
			<div
				class="flex h-full w-full flex-col rounded-md border bg-white text-left drop-shadow-lg">
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

						<BasicButton>Speichere</BasicButton>
					</form>
					<div class="mb-10 w-full p-3">
						<div v-if="timePreview">
							<TitleItem class="mb-2">Spielinformatione</TitleItem>
							<h1 class="text-2xl font-medium">
								Ahzahl Spiel:
								<span class="font-normal">{{ timePreview.amountOfGames }}</span>
							</h1>
							<h1 class="text-2xl font-medium">
								S letschte reguläre Spiel endet am:
								<span class="font-normal">{{
									formatDate(new Date(timePreview.lastGame), "HH:mm")
								}}</span>
							</h1>
						</div>
					</div>
				</div>
				<div class="flex justify-between">
					<router-link v-if="backRoute" :to="{ name: backRoute }"
						><button class="p-3"
							><ArrowLeft/></button
						></router-link
					>
					<router-link v-if="nextRoute" :to="{ name: nextRoute }"
						><button
							@click="$router.push({ to: nextRoute })"
							class="p-3"
							><ArrowRight/></button
						></router-link
					>
				</div>
			</div>
		</div>
	</div>
</template>

<script>
import BasicButton from "@/components/BasicButton.vue"
import JsonForm from "@/components/JsonForm.vue"
import { format } from "date-fns"
import TitleItem from "@/components/TitleItem.vue"
import StepOverview from "@/components/StepOverview.vue"
import ArrowLeft from "@/components/icons/ArrowLeft.vue"
import ArrowRight from "@/components/icons/ArrowRight.vue"
export default {
	components: { BasicButton, JsonForm, TitleItem, StepOverview, ArrowLeft, ArrowRight },
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
						label: "Startziit",
						model: "startTime",
						component: "TextField",
						type: "time",
					},
				],
				[
					{
						label: "Spielziit",
						model: "gameDuration",
						component: "TextField",
						type: "time",
					},
				],
				[
					{
						label: "Pauseziit",
						model: "breakDuration",
						component: "TextField",
						type: "time",
					},
				],
			],
			steps: [
				{ route: "halls", name: "Halle" },
				{ route: "sections", name: "Abteilige" },
				{ route: "categories", name: "Kategorie" },
				{ route: "teams", name: "Teams" },
				{ route: "options", name: "Ihstellige" },
			],
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
			this.item.startedTournament = true
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
