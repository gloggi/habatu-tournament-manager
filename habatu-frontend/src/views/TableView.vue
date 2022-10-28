<template>
	<TableHeader />
	<div class="mt-2 rounded-md border bg-white">
		<div
			v-if="timeslots && halls"
			:key="tableKey"
			class="mt-3 mb-3 h-full w-full space-y-2 px-2">
			<div
				class="hidden items-baseline justify-center space-x-4 md:flex md:flex-row">
				<div class="w-1/2 2xl:w-1/4"></div>
				<div
					v-for="hall in halls"
					:key="hall._id"
					class="w-full text-xl font-light">
					{{ hall.name }}
				</div>
			</div>
			<div
				:class="`flex flex-col items-baseline justify-center space-y-0 md:flex-row md:space-x-4 ${
					timeslots[timeslot].isNow
						? 'rounded-lg ring-2 ring-red-400 ring-opacity-75 ring-offset-1'
						: ''
				}`"
				v-for="timeslot in Object.keys(timeslots)"
				:key="timeslot">
				<div class="w-1/2 self-center text-center text-xl font-light 2xl:w-1/4">
					{{ timeslot }}
				</div>
				<DragSlot
					class="w-full self-stretch"
					:hall="timeslots[timeslot].items[hall].id"
					:timeslot="timeslots[timeslot].id"
					v-for="hall in Object.keys(timeslots[timeslot].items)"
					:key="hall.name">
					<div class="text-center md:hidden">{{ hall }}</div>
					<GameField :games="timeslots[timeslot].items[hall].items" />
				</DragSlot>
			</div>
		</div>
	</div>

	<GameModal />
</template>

<script>
import { format } from "date-fns"
import DragSlot from "@/components/DragSlot.vue"
import GameField from "@/components/GameField.vue"
import GameModal from "@/components/GameModal.vue"
import TableHeader from "@/components/TableHeader.vue"

export default {
	name: "TableView",
	data() {
		return {
			tableKey: 0,
			timeslotNow: undefined,
		}
	},
	components: {
		DragSlot,
		GameField,
		GameModal,
		TableHeader,
	},
	computed: {
		timeslots(){
			return this.$store.state.tournament.table
		},
		halls(){
			return this.$store.state.halls.halls
		}
	},
	methods: {
		handleReload() {
			this.getTimeslots()
		},
		dragStartHandler(evt, gameId) {
			evt.dataTransfer.setData("gameId", gameId)
		},
		format(date, form) {
			return format(date, form)
		}
	},
	async created() {
		await this.$store.dispatch("tournament/getTable")
		await this.$store.dispatch("halls/get")
		this.getHalls()
		await this.$store.dispatch("games/get")
	},
}
</script>
