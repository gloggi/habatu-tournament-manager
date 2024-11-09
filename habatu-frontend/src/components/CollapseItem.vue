<template>
	<div class="flex w-full flex-col">
		<button
			@click="show = !show"
			class="top-lg w-full cursor-pointer rounded bg-gray-900 text-white p-3 text-left text-lg">
			{{ title }}
		</button>

		<Transition name="collapse">
			<div class="w-full overflow-hidden bg-white p-3 px-5" v-if="show">
				<slot></slot>
			</div>
		</Transition>
	</div>
</template>

<script>
export default {
	props: ["title"],
	data() {
		return {
			show: false,
		}
	},
}
</script>

<style>
.collapse-enter-active {
	animation: collapse reverse 300ms cubic-bezier(0, 0, 1, 1);
}

.collapse-leave-active {
	animation: collapse 300ms cubic-bezier(0, 0, 1, 1);
}

@keyframes collapse {
	from {
		max-height: 200px;
	}

	to {
		max-height: 0px;
		padding-top: 0;
		padding-bottom: 0;
	}
}
</style>
