<template>
	<div class="md:flex md:justify-center md:items-center">
	<div
		v-if="game"
		class="-m-2 h-full min-h-screen w-screen md:w-1/3 text-white"
		:style="`${
			!gameOver
				? 'background-color: rgb(34 197 94)'
				: 'background-color: rgb(239 68 68)'
		}`">
		<div class="flex flex-col space-y-2">
			<div class="flex flex-col items-center space-x-1 p-3">
				<div class="font-medium">Timer</div>
				<div class="text-7xl font-medium">{{ timer }}</div>
			</div>
			<div class="flex flex-col justify-around text-center">
				<div class="flex flex-row">
					<div class="w-1/2 text-2xl font-medium">
						{{ game.teamA.name }}
					</div>
					<div class="w-1/2 text-2xl font-medium">
						{{ game.teamB.name }}
					</div>
				</div>
				<div class="flex flex-row">
					<div class="mb-2 w-1/2 text-2xl font-light">
						{{ game.teamA.section.name }}
					</div>
					<div class="mb-2 w-1/2 text-2xl font-light">
						{{ game.teamB.section.name }}
					</div>
				</div>
				<div class="flex flex-row">
					<button class="w-1/2 p-4" @click="modifyPoints('TeamA', 1)">
						<CaretUpFillIcon class="w-full" />
					</button>
					<button class="w-1/2 p-4" @click="modifyPoints('TeamB', 1)">
						<CaretUpFillIcon class="w-full" />
					</button>
				</div>
				<div class="flex flex-row">
					<div class="mb-4 w-1/2 text-9xl font-medium">
						{{ game.pointsTeamA }}
					</div>
					<div class="mb-4 w-1/2 text-9xl font-medium">
						{{ game.pointsTeamB }}
					</div>
				</div>
				<div class="flex flex-row">
					<button class="w-1/2 p-4" @click="modifyPoints('TeamA', -1)">
						<CaretDownFillIcon class="w-full" />
					</button>
					<button class="w-1/2 p-4" @click="modifyPoints('TeamB', -1)">
						<CaretDownFillIcon class="w-full" />
					</button>
				</div>
			</div>
			<div class="flex flex-col space-y-2 p-5">
				<BasicButton
					class="bg-green-700 hover:bg-green-700"
					v-if="!started"
					@click="startTimer"
					>Timer starte</BasicButton
				>
				<BasicButton
					class="bg-orange-700 hover:bg-orange-700"
					v-if="!started"
					@click="clearTimer"
					>Timer nomal vo vorne</BasicButton
				>
				<BasicButton
					class="bg-red-500 hover:bg-red-500"
					v-if="started"
					@click="stopTimer"
					>Stop</BasicButton
				>
			</div>
			<div class="flex flex-col space-y-2 p-5">
				<BasicButton class="bg-black hover:bg-black" @click="reportGame"
					>Spielstand abschicke</BasicButton
				>
			</div>
		</div>
	</div>
</div>
</template>

<script>
import BasicButton from "@/components/BasicButton.vue"
import CaretDownFillIcon from "@/components/icons/CaretDownFillIcon.vue"
import CaretUpFillIcon from "@/components/icons/CaretUpFillIcon.vue"
import { intervalToDuration } from "date-fns"

export default {
	data() {
		return {
			gameId: undefined,
			game: undefined,
			startTime: undefined,
			milliseconds: 0,
			interval: undefined,
			started: false,
			gameDuration: undefined,
			gameOver: false,
		}
	},
	methods: {
		async getGame() {
			try {
				const response = await this.callApi("get", `/games/${this.gameId}`)
				this.game = response.data
			} catch (e) {
				console.log(e)
			}
		},
		async modifyPoints(team, value) {
			if (this.game[`points${team}`] + value >= 0) {
				this.game[`points${team}`] += value
			}
			localStorage.setItem(
				`${this.game._id}:pointsTeamA`,
				this.game.pointsTeamA
			)
			localStorage.setItem(
				`${this.game._id}:pointsTeamB`,
				this.game.pointsTeamB
			)
			await this.callApi("put", `/games/${this.gameId}`, {
					pointsTeamA: this.game.pointsTeamA,
					pointsTeamB: this.game.pointsTeamB,
				})
		},
		startTimer() {
			this.started = true
			this.interval = setInterval(() => {
				if (this.milliseconds == 0) {
					clearInterval(this.interval)
					this.started = false
					this.gameOver = true
					return
				}
				this.milliseconds -= 100
			}, 100)
		},
		stopTimer() {
			this.started = false
			clearInterval(this.interval)
		},
		clearTimer() {
			this.milliseconds = this.gameDuration
		},
		zeropad(num) {
			return String(num).padStart(2, "0")
		},
		async reportGame() {
			try {
				await this.callApi("put", `/games/${this.gameId}`, {
					pointsTeamA: localStorage.getItem(`${this.game._id}:pointsTeamA`),
					pointsTeamB: localStorage.getItem(`${this.game._id}:pointsTeamB`),
				})
				this.$router.push({ name: "menu" })
			} catch (e) {
				this.$store.commit("notifications/showNotification", {
					message: "Kei Verbindig!",
					type: false,
				})
			}
		},
	},
	computed: {
		timer() {
			const duration = intervalToDuration({ start: 0, end: this.milliseconds })
			return `${this.zeropad(duration.minutes)}:${this.zeropad(
				duration.seconds
			)}`
		},
	},
	async created() {
		this.gameId = this.$route.params.id
		this.gameDuration =
			parseInt(this.$store.state.options.options.gameDuration.split(":")[1]) *
			60 *
			1000
		this.milliseconds = this.gameDuration
		await this.getGame()
		var pointsTeamA = localStorage.getItem(`${this.game._id}:pointsTeamA`)
		var pointsTeamB = localStorage.getItem(`${this.game._id}:pointsTeamB`)
		if (pointsTeamA && pointsTeamA) {
			this.game.pointsTeamA = parseInt(pointsTeamA)
			this.game.pointsTeamB = parseInt(pointsTeamB)
		}
	},
	components: { CaretUpFillIcon, CaretDownFillIcon, BasicButton },
}
</script>

<style></style>
