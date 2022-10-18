<template>
	<div class="min-h-screen bg-gray-100 p-2">
		<router-view />
		<NotificationItem />
	</div>
</template>
<script>
import NotificationItem from "./components/NotificationItem.vue"

export default {
	async created() {
		
		await this.$store.dispatch("user/getMe", localStorage.token)
		await this.$store.dispatch("games/get")
		await this.$store.dispatch("halls/get")
		await this.$store.dispatch("categories/get")
		await this.$store.dispatch("options/get")
		await this.$store.dispatch("sections/get")
		await this.$store.dispatch("teams/get")
		await this.$store.dispatch("timeslots/get")
	},
	components: { NotificationItem },
}
</script>

<style>
body {
	overflow-x: hidden;
}
.no-scrollbar::-webkit-scrollbar {
	display: none;
}

/* Hide scrollbar for IE, Edge and Firefox */
.no-scrollbar {
	-ms-overflow-style: none; /* IE and Edge */
	scrollbar-width: none; /* Firefox */
}
</style>
