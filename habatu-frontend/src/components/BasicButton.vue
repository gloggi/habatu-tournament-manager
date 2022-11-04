<template>
	<button
		@click="handleClick"
		:class="`w-full rounded-md ${
			disabled ? 'cursor-not-allowed bg-gray-700' : 'bg-gray-900'
		} p-2 text-white hover:bg-gray-700`">
		<slot></slot>
	</button>
</template>

<script>
export default {
	props: ["disabled"],
	methods: {
		handleClick(evt) {
			if (this.disabled) {
				evt.preventDefault()
			}
		},
	},
}
</script>

<style></style>
