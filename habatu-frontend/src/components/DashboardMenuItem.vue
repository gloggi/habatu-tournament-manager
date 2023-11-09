<template>
	<router-link :to="{ name: to }" @click="clicked">
		<div
			class="m-1 flex items-center space-x-4 rounded-md bg-gray-900 p-2 px-4 text-white hover:bg-gray-800">
			<div><slot></slot></div>
			<div>{{ title }}</div>
		</div>
	</router-link>
</template>

<script>
export default {
	props: ["title", "to"],
	emits: ["clicked"],
	methods: {
		clicked(){
			this.$emit("clicked", true)
		}
	},
}
</script>

<style></style>
