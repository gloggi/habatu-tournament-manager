<template>
	<div class="flex flex-col space-y-2">
		<SelectField v-model="value" :options="options" />
		<p>{{ value }}</p>
	</div>
</template>

<script>
import SelectField from "@/components/SelectField.vue"
export default {
	components: { SelectField },
	data() {
		return {
			value: 0,
			options: [
				{ _id: 0, name: "Max Mustermann" },
				{ _id: 1, name: "John Doe" },
				{ _id: 3, name: "Jane Doe" },
				{ _id: 3, name: "Erika Mustermann" },
			],
		}
	},
}
</script>

<style></style>
