<template>
	<div class="flex w-full flex-col space-y-3" v-for="(row, i) in form" :key="i">
		<div class="flex w-full justify-between space-x-2">
			<template v-for="(col, j) in row" :key="j">
				<TextInput
					v-if="col.component == 'TextField'"
					class="w-full"
					v-model="item[col.model]"
					:label="col.label"
					:type="col.type" />
				<SelectField
					v-if="col.component == 'SelectField'"
					:options="col.options"
					class="w-full"
					v-model="item[col.model]"
					:label="col.label" />
				<ColorPicker
					v-if="col.component == 'ColorPicker'"
					v-model="item[col.model]"
					:label="col.label" />
				<div v-if="!col.component" :class="`text-2xl font-medium ${col.class}`">
					{{ item[col.model].name ? item[col.model].name : item[col.model] }}
				</div>
			</template>
		</div>
	</div>
</template>

<script>
import TextInput from "@/components/TextInput.vue"
import SelectField from "@/components/SelectField.vue"
import ColorPicker from "./ColorPicker.vue"
export default {
	components: { TextInput, SelectField, ColorPicker },
	props: ["form", "values"],
	emits: ["changeForm"],
	data() {
		return {
			item: {},
		}
	},
	watch: {
		item: {
			handler(newVal) {
				this.$emit("changeForm", newVal)
			},
			deep: true,
		},
	},
	created() {
		this.values ? (this.item = this.values) : ""
	},
}
</script>

<style></style>
