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
			name: "Abteilungen",
			state: "sections",
			nextRoute: "categories",
			backRoute: undefined,
			form: [[{ label: "Abteilung", model: "name", component: "TextField" }]],
		}
	},
	methods: {},
}
</script>

<style></style>
