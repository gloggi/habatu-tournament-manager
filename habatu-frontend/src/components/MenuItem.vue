<template>
	<router-link :to="to">
		<div
			class="flex aspect-square h-full flex-col justify-end rounded-md border bg-white p-3 text-gray-700 drop-shadow-lg">
			<div class="h-2/3">
				<slot></slot>
			</div>
			<div
				class="flex h-1/3 flex-col justify-center text-center align-baseline text-xl font-light md:text-3xl">
				<p>{{ name }}</p>
			</div>
		</div>
	</router-link>
</template>

<script>
export default {
	props: ["name", "to"],
}
</script>

<style></style>
