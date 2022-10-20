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
		await this.$store.dispatch("users/get")
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
/* Firefox */
* {
	scrollbar-width: auto;
	scrollbar-color: #b8b8b8 transparent;
}

/* Chrome, Edge, and Safari */
*::-webkit-scrollbar {
	width: 10px;
}

*::-webkit-scrollbar-track {
	background: transparent;
}

*::-webkit-scrollbar-thumb {
	background-color: #b8b8b8;
	border-radius: 10px;
	border: 3px solid #ffffff;
}
</style>
