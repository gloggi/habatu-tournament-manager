<template>
	<div class="p-3">
		<div class="flex h-full flex-col items-stretch">
			<StepOverview :steps="steps" class="h-20" />
			<div
				class="flex h-full w-full flex-col rounded-md border bg-white text-left drop-shadow-lg">
				<div class="flex" style="height: 70vh">
					<form
						@submit.prevent="createItem"
						:key="formKey"
						class="flex w-full flex-col space-y-3 border-r p-5">
						<TitleItem class="pb-3">{{ name }}</TitleItem>
						<JsonForm @changeForm="handleMainFormChange" :form="form" />

						<BasicButton>Create</BasicButton>
					</form>
					<div class="mb-10 w-full p-3">
						<div
							class="no-scrollbar h-full overflow-scroll rounded-lg border bg-gray-100">
							<CollapseItem
								v-for="item in items"
								:title="item.name"
								:key="item._id">
								<div class="flex flex-col">
									<div class="w-full space-y-3">
										<JsonForm
											@changeForm="handleSideFormChange"
											:form="form"
											:values="item" />
									</div>
									<div class="flex justify-end space-x-2 self-end">
										<div class="mt-2">
											<button @click="updateItem(item._id)">
												<RefreshIcon />
											</button>
										</div>
										<div class="mt-2">
											<button @click="deleteItem(item._id)">
												<TrashIcon />
											</button>
										</div>
									</div>
								</div>
							</CollapseItem>
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
import CollapseItem from "@/components/CollapseItem.vue"
import TrashIcon from "../components/icons/TrashIcon.vue"
import JsonForm from "@/components/JsonForm.vue"
import RefreshIcon from "@/components/icons/RefreshIcon.vue"
import TitleItem from "@/components/TitleItem.vue"
import StepOverview from "@/components/StepOverview.vue"
import ArrowLeft from "@/components/icons/ArrowLeft.vue"
import ArrowRight from "@/components/icons/ArrowRight.vue"
export default {
	components: {
    BasicButton,
    CollapseItem,
    TrashIcon,
    JsonForm,
    RefreshIcon,
    TitleItem,
    StepOverview,
    ArrowLeft,
    ArrowRight
},
	props: ["name", "form", "state", "nextRoute", "backRoute"],
	data() {
		return {
			item: {},
			formKey: 0,
			toUpdateItems: {},
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
			return this.$store.state[this.state][this.state]
		},
	},
	methods: {
		deleteItem(id) {
			this.$store.dispatch(`${this.state}/delete`, id)
		},
		createItem() {
			this.$store.dispatch(`${this.state}/create`, this.item)
			this.formKey++
			setTimeout(()=>document.querySelector('input').focus(),10)
			
		},
		updateItem(itemId) {
			if (this.toUpdateItems[itemId]) {
				this.$store.dispatch(`${this.state}/update`, this.toUpdateItems[itemId])
				this.formKey++
				this.toUpdateItems[itemId] = undefined
			}
		},
		handleMainFormChange(item) {
			this.item = item
		},
		handleSideFormChange(item) {
			this.toUpdateItems[item._id] = item
		},
	},
	created() {
		if (!this.userIsAdmin()) {
			//this.$router.push({name: "menu"})
		}
	},
}
</script>

<style></style>
