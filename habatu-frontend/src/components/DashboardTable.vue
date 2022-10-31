<template>
	<div class="mb-4 rounded-md bg-white p-3">
		<TitleItem>{{ name }}</TitleItem>
	</div>
	<div v-if="items" class="flex flex-col">
		<div class="flex rounded-t-md border bg-white p-3 font-medium">
			<template v-for="key in Object.keys(items[0])" :key="key">
				<div
					v-if="formatValue(items[0][key])"
					:style="`width:${Math.floor((1 / columns) * 100)}%`"
					class="cursor-default">
					{{ key }}
				</div>
			</template>
		</div>
		<router-link v-for="item in items" :key="item._id" :to="{name: `${state}Edit` ,params: {id: item._id}}" >
			<div
				class="justyfy-start flex cursor-pointer rounded-md border bg-white p-3 hover:bg-gray-50">
				<template v-for="(key, i) in Object.keys(item)" :key="i">
					<div
						v-if="formatValue(item[key])"
						:style="`width:${Math.floor((1 / columns) * 100)}%`">
						{{ formatValue(item[key]) }}
					</div>
				</template>
			</div>
		</router-link>
	</div>
</template>

<script>
import { format } from "date-fns"
import TitleItem from "./TitleItem.vue"
export default {
	props: ["state", "name"],
	methods: {
		deleteItem(id) {
			this.$store.dispatch(`${this.state}/delete`, id)
		},
		createItem() {
			this.$store.dispatch(`${this.state}/create`, this.item)
			this.formKey++
		},
		formatValue(value) {
			if (value.name) {
				return value.name
			}
			if (value.startTime) {
				return `${format(new Date(value.startTime), "HH:mm")} - ${format(
					new Date(value.endTime),
					"HH:mm"
				)}`
			}
			if (typeof value == "string" && value.length != 24) {
				return value
			}
			return
		},
	},
	computed: {
		items() {
			return this.$store.state[this.state][this.state]
		},
		columns() {
			return this.items.length > 0
				? Object.keys(this.items[0]).filter(i =>
						this.formatValue(this.items[0][i])
                ).length
				: undefined
		},
	},
	components: { TitleItem },
}
</script>

<style></style>
