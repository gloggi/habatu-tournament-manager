<template>
	<div class="flex flex-col space-y-2">
		<ColorPicker label="Test" v-model="color" />
	</div>
</template>

<script>
import ColorPicker from "@/components/ColorPicker.vue"
export default {
	data() {
		return {
			value: 0,
			color: "#123456",
			options: [
				{ _id: 0, name: "Max Mustermann" },
				{ _id: 1, name: "John Doe" },
				{ _id: 3, name: "Jane Doe" },
				{ _id: 3, name: "Erika Mustermann" },
			],
		}
	},
	components: { ColorPicker },
}
</script>

<style></style>
