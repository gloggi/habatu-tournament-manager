<template>
	<Transition name="list">
		<div v-if="show" class="sticky bottom-0 w-full overflow-x-hidden">
			<div class="flex w-full justify-end p-3">
				<div
					v-if="show"
					class="bottom-3 left-3 right-3 flex w-1/4 items-center space-x-3 rounded-lg border bg-white p-3 drop-shadow-lg">
					<CheckIcon v-if="type" class="text-green-600" />
					<XCircleFillIcon v-else class="text-red-600" />
					<div class="text font-bold">{{ message }}</div>
				</div>
			</div>
		</div>
	</Transition>
</template>

<script>
import CheckIcon from "./icons/CheckIcon.vue"
import XCircleFillIcon from "./icons/XCircleFillIcon.vue"
export default {
	components: { CheckIcon, XCircleFillIcon },
	data() {
		return {}
	},
	computed: {
		show() {
			return this.$store.state.notifications.show
		},
		message() {
			return this.$store.state.notifications.message
		},
		type() {
			return this.$store.state.notifications.type
		},
	},
}
</script>

<style>
.list-enter-active,
.list-leave-active {
	transition: all 0.5s ease;
}
.list-enter-from,
.list-leave-to {
	opacity: 0;
	transform: translateX(30px);
}
</style>
