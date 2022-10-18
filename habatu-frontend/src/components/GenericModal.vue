<template>
	<div
		class="sticky bottom-1/4 left-1/4 z-50 w-1/2 self-center rounded-md border bg-white p-5 pt-20 drop-shadow-lg">
		<div class="r-2 absolute top-5 text-2xl font-black">
			{{ title }}
		</div>
		<button
			@click="close"
			class="absolute top-0 right-0 rounded-bl-md rounded-tr-md border p-5 text-xl">
			<XIcon />
		</button>
		<div class="no-scrollbar h-full w-full overflow-scroll">
			<div class="flex flex-col space-y-2">
				<slot></slot>
			</div>
		</div>
	</div>
</template>

<script>
import XIcon from "./icons/XIcon.vue"
export default {
	components: { XIcon },
	props: ["title", "close"],
}
</script>

<style></style>
