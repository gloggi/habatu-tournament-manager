<template>
	<div class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full"></div>
	<div
		class="fixed inset-28 z-50 rounded-md border bg-white p-5 pt-20 drop-shadow-lg">
		<div class=" absolute top-5">
			<TitleItem>{{title}}</TitleItem>
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
import TitleItem from "./TitleItem.vue";
export default {
	components: { XIcon, TitleItem },
	props: ["title", "close"],
}
</script>

<style></style>
