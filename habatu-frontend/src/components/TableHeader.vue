<template>
	<div class="">
		<div
			class="flex w-full items-center justify-between rounded-md border bg-white p-3">
			<router-link :to="{ name: 'menu' }">
				<div class="flex items-center space-x-3">
					<img src="@/assets/rotating_ball_a.png" class="h-16 w-16" />
					<h1 class="text-2xl font-black text-gray-800 md:text-3xl">
						HaBaTu {{new Date().getFullYear()}}
					</h1>
				</div>
			</router-link>
			<div class="text-2xl font-black text-gray-800 md:text-6xl">
				{{ time }}
			</div>
		</div>
	</div>
</template>

<script>
import { format } from "date-fns"
export default {
	data() {
		return {
			time: undefined,
		}
	},
	methods: {
		setTime() {
			this.time = format(new Date(), "HH:mm:ss")
		},
	},
	created() {
		setInterval(this.setTime, 1000)
	},
}
</script>

<style></style>
