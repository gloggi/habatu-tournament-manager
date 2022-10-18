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
			name: "Kategorien",
			state: "categories",
			nextRoute: "teams",
			backRoute: "sections",
			form: [[{ label: "Kategorie", model: "name", component: "TextField" }],
			[{ label: "Gruppenfarbe", model: "color", component: "ColorPicker" }]],
		}
	},
	methods: {},
}
</script>

<style></style>
