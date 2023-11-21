<template>
	<div class="mb-4 rounded-md bg-white p-3">
		<TitleItem>{{ name }}</TitleItem>
	</div>
	<div v-if="items" class="flex flex-col">
		<div class="flex rounded-t-md border bg-white p-3 font-medium">
			<template v-for="key in tableKeys" :key="key">
				<div :style="`width:${Math.floor((1 / tableKeys.length) * 100)}%`"
					class="cursor-default">
					{{ key }}
				</div>
			</template>
		</div>
		<router-link v-for="item in items" :key="item._id" :to="{ name: `${state}Edit`, params: { id: item._id } }">
			<div class="justyfy-start flex cursor-pointer rounded-md border bg-white p-3 hover:bg-gray-50">
				<template v-for="(key, i) in tableKeys" :key="i">
					<div
						:style="`width:${Math.floor((1 / tableKeys.length) * 100)}%`">
						{{  formatValue(item[key]) }}
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
	props: ["state", "name", "keys"],
	data() {
		return {  }
	},
	methods: {
		deleteItem(id) {
			this.$store.dispatch(`${this.state}/delete`, id)
		},
		createItem() {
			this.$store.dispatch(`${this.state}/create`, this.item)
			this.formKey++
		},
		formatValue(value) {
			if (value && value.name) {
				return value.name
			}
			if (value && value.startTime) {
				return `${format(new Date(value.startTime), "HH:mm")} - ${format(
					new Date(value.endTime),
					"HH:mm"
				)}`
			}
			if (value && typeof value == "string" && value.length != 24) {
				return value
			}
			if (!value) {
				return "-"
			}
			return
		},
	},
	computed: {
		items() {
			return this.$store.state[this.state][this.state]
		},
		tableKeys() {
			return this.keys.split(",")
		}
	},
	components: { TitleItem },
	async created() {
		await this.$store.dispatch(`${this.state}/get`, this.item)

	}
}
</script>

<style></style>
