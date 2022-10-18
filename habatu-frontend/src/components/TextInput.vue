<template>
	<div class="">
		<label class="mb-1 block text-sm font-bold text-gray-700" for="username">
			{{ label }}
		</label>
		<input
			autocomplete="off"
			v-model="payload"
			class="focus:shadow-outline w-full appearance-none rounded border border-black py-2 px-3 leading-tight text-gray-700 focus:outline-none focus:ring-1 focus:ring-gray-400"
			:id="label"
			:type="type"
			:placeholder="label" />
	</div>
</template>

<script>
import { computed } from "vue"
export default {
	props: ["label", "type", "modelValue"],
	setup(props, { emit }) {
		const payload = computed({
			get: () => props.modelValue,
			set: value => emit("update:modelValue", value),
		})

		return {
			payload,
		}
	},
}
</script>

<style></style>
