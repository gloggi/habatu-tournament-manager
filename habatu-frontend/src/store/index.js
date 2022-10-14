import { createStore } from 'vuex'
import { gameModal } from './modules/gameModal'
import { games } from './modules/games'
import { halls } from './modules/halls'
import { categories } from './modules/categories'
import { options } from './modules/options'
import { sections } from './modules/sections'
import { teams } from './modules/teams'
import { timeslots } from './modules/timeslots'

export default createStore({
  modules: {
    gameModal,
    games,
    halls,
    categories,
    options,
    sections,
    teams,
    timeslots

  }
})