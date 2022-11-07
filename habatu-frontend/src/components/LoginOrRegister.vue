<template>
	<div class="flex w-full justify-center space-x-2 p-2">
		<button
			@click="handleSwitch"
			:class="`w-1/4 rounded-md p-1  text-xs ${
				login
					? 'bg-gray-900 text-white'
					: 'border hover:bg-gray-900 hover:text-white'
			} `">
			Ihlogge
		</button>
		<button
			@click="handleSwitch"
			:class="`w-1/4 rounded-md p-1  text-xs ${
				!login
					? 'bg-gray-900 text-white'
					: 'border hover:bg-gray-900 hover:text-white'
			} `">
			Registriere
		</button>
	</div>
</template>

<script>
export default {
	props: ["login"],
	emits: ["switch"],
	data() {
		return {}
	},
	methods: {
		handleSwitch() {
			this.$emit("switch", !this.login)
		},
	},
}
</script>

<style></style>
