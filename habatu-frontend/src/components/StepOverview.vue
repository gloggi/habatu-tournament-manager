<template>
	<div>
		<div
			class="relative after:absolute after:inset-x-0 after:top-1/2 after:block after:h-0.5 after:-translate-y-1/2 after:rounded-lg after:bg-gray-300">
			<ol
				class="relative z-10 flex justify-between text-sm font-medium text-gray-500">
				<li
					class="flex items-center bg-gray-100 p-2"
					v-for="(step, i) in steps"
					:key="step.route">
					<span
						:class="`h-6 w-6 rounded-full ${
							routeName == step.route ? 'bg-blue-600 text-white' : 'bg-gray-300'
						} text-center text-[10px] font-bold leading-6`">
						{{ i + 1 }}
					</span>

					<span class="hidden sm:ml-2 sm:block"
						><router-link :to="{ name: step.route }" class="h-full w-full">
							{{ step.name }}
						</router-link></span
					>
				</li>
			</ol>
		</div>
	</div>
</template>

<script>
export default {
	props: ["steps"],
	computed: {
		routeName() {
			return this.$route.name
		},
	},
}
</script>

<style></style>
