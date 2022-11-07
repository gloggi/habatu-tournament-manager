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
			name: "Abteilig",
			state: "sections",
			nextRoute: "categories",
			backRoute: "halls",
			form: [[{ label: "Abteilig", model: "name", component: "TextField" }]],
		}
	},
	methods: {},
}
</script>

<style></style>
