<template>
	<div class="grid grid-cols-2 gap-4 md:grid-cols-5">
		<div
			class="col-span-2 flex justify-between rounded-md border bg-white p-3 drop-shadow-lg md:col-span-5">
			<h1 v-if="nickname" class="text-2xl font-semibold">
				Hoi, {{ nickname }}
			</h1>
			<button @click="logout">Logout</button>
		</div>
		<MenuItem
			name="Spiel erstellen"
			v-if="!gameStarted && userIsAdmin()"
			to="halls">
			<JoystickIcon class="h-full w-full" />
		</MenuItem>
		<MenuItem name="Spielplan" to="table">
			<MapIcon class="h-full w-full" />
		</MenuItem>
		<MenuItem name="Reglä" to="rules">
			<BookIcon class="h-full w-full" />
		</MenuItem>
		<MenuItem name="Ranglischtä" to="ranking">
			<TrophyIcon class="h-full w-full" />
		</MenuItem>
		<MenuItem v-if="false" name="Grüchtli" to="rumors">
			<ChatHeartIcon class="h-full w-full" />
		</MenuItem>
		<MenuItem name="Mis Team" to="team">
			<PeopleIcon class="h-full w-full" />
		</MenuItem>
		<MenuItem v-if="userIsReferee()" name="Schiri" to="referee">
			<EyeglassesIcon class="h-full w-full" />
		</MenuItem>
		<MenuItem v-if="userIsAdmin()" name="Admin" to="dashboard">
			<SpeedometerIcon class="h-full w-full" />
		</MenuItem>
		<MenuItem name="Ich" to="me">
			<PersonCircleIcon class="h-full w-full" />
		</MenuItem>
	</div>
</template>

<script>
import MenuItem from "@/components/MenuItem.vue"
import MapIcon from "../components/icons/MapIcon.vue"
import TrophyIcon from "../components/icons/TrophyIcon.vue"
import SpeedometerIcon from "../components/icons/SpeedometerIcon.vue"
import ChatHeartIcon from "../components/icons/ChatHeartIcon.vue"
import PeopleIcon from "../components/icons/PeopleIcon.vue"
import BookIcon from "../components/icons/BookIcon.vue"
import EyeglassesIcon from "../components/icons/EyeglassesIcon.vue"
import JoystickIcon from "../components/icons/JoystickIcon.vue"
import PersonCircleIcon from "@/components/icons/PersonCircleIcon.vue"
export default {
	components: {
		MenuItem,
		MapIcon,
		TrophyIcon,
		SpeedometerIcon,
		ChatHeartIcon,
		PeopleIcon,
		BookIcon,
		EyeglassesIcon,
		JoystickIcon,
		PersonCircleIcon,
	},
	computed: {
		nickname() {
			return this.$store.state.user.user.nickname
		},
		gameStarted() {
			return this.$store.state.options.options?.startedTournament
		},
	},
	methods: {
		logout() {
			localStorage.removeItem("token")
			this.$router.push("login")
		},
	},
}
</script>

<style></style>
