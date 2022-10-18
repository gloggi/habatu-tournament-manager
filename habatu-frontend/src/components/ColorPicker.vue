<template>
	<div class="flex items-center space-x-2">
		<label class="mb-1 block text-sm font-bold text-gray-700" for="pickColor">
			{{ label }}
		</label>
		<div class="relative">
			<input
				id="pickColor"
				class="absolute h-8 w-8 opacity-0"
				value="modelValue"
				@change="$emit('update:modelValue', $event.target.value)"
				type="color" />
			<div
				class="h-8 w-8 rounded-full border-2 border-white ring-1 ring-gray-500"
				:style="`background-color: ${modelValue}`"></div>
		</div>
	</div>
</template>

<script>
export default {
	props: ["modelValue", "label"],
	emits: ["update:modelValue"],
}
</script>

<style></style>
