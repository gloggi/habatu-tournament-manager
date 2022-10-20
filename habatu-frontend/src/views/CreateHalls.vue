<template>
	<CreateView
		:nextRoute="nextRoute"
		:backRoute="backRoute"
		:name="name"
		:state="state"
		:form="form" />
</template>

<script>
import CreateView from "./CreateView.vue"
export default {
	components: { CreateView },
	data() {
		return {
			name: "Hallen",
			state: "halls",
			nextRoute: "sections",
		}
	},
	methods: {},
	computed: {
		form() {
			return [[{ label: "Hallenname", model: "name", component: "TextField" }]]
		},
	},
}
</script>

<style></style>
