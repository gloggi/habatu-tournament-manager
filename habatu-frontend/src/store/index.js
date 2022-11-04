import { createStore } from "vuex"
import { gameModal } from "./modules/gameModal"
import { games } from "./modules/games"
import { halls } from "./modules/halls"
import { categories } from "./modules/categories"
import { options } from "./modules/options"
import { sections } from "./modules/sections"
import { teams } from "./modules/teams"
import { timeslots } from "./modules/timeslots"
import { notifications } from "./modules/notifications"
import { user } from "./modules/user"
import { users } from "./modules/users"
import { tournament } from "./modules/tournament"

export default createStore({
	modules: {
		gameModal,
		games,
		halls,
		categories,
		options,
		sections,
		teams,
		timeslots,
		notifications,
		user,
		users,
		tournament,
	},
})
