<template>
	<div :key="labelKey">
		<label class="mb-1 block text-sm font-bold text-gray-700" for="username">
			{{ label }}
		</label>
		<div
			class="relative mr-2 inline-block w-10 select-none align-middle transition duration-200 ease-in">
			<input
				type="checkbox"
				:ref="`checkbox-${label}`"
				@change="handleChange"
				:name="`toggle-${label}`"
				class="toggle-checkbox absolute block h-6 w-6 cursor-pointer appearance-none rounded-full border-4 bg-white" />
			<label
				for="toggle"
				class="toggle-label block h-6 cursor-pointer overflow-hidden rounded-full bg-gray-300"></label>
		</div>
	</div>
</template>

<script>
export default {
	props: ["modelValue", "label"],
	data() {
		return {
			labelKey: 0,
		}
	},
	watch: {},
	methods: {
		handleChange(event) {
			event.target.checked = true
			this.$refs[`checkbox-${this.label}`].checked = !this.modelValue
			this.$emit("update:modelValue", !this.modelValue)
		},
	},
	mounted() {
		this.$refs[`checkbox-${this.label}`].checked = this.modelValue
	},
}
</script>

<style>
.toggle-checkbox:checked {
	@apply: right-0 border-green-400;
	right: 0;
	border-color: #000000;
}
.toggle-checkbox:checked + .toggle-label {
	@apply: bg-green-400;
	background-color: #000000;
}
</style>
