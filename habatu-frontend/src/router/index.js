import { createRouter, createWebHistory } from "vue-router"
import TableView from "../views/TableView.vue"
import LoginView from "../views/LoginView.vue"
import TestView from "../views/TestView"
import CreateView from "../views/CreateView"
import CreateSections from "../views/CreateSections"
import CreateCategories from "../views/CreateCategories"
import CreateTeams from "../views/CreateTeams"
import CreateOptions from "../views/CreateOptions"
import RankingView from "../views/RankingView"
import MenuView from "../views/MenuView"
import TeamView from "../views/TeamView"

const routes = [
	{
		path: "/",
		name: "menu",
		component: MenuView,
	},
	{
		path: "/table",
		name: "table",
		component: TableView,
	},
	{
		path: "/login",
		name: "login",
		component: LoginView,
	},
	{
		path: "/test",
		name: "test",
		component: TestView,
	},
	{
		path: "/create",
		name: "create",
		component: CreateView,
	},
	{
		path: "/sections",
		name: "sections",
		component: CreateSections,
	},
	{
		path: "/categories",
		name: "categories",
		component: CreateCategories,
	},
	{
		path: "/teams",
		name: "teams",
		component: CreateTeams,
	},
	{
		path: "/options",
		name: "options",
		component: CreateOptions,
	},
	{
		path: "/ranking",
		name: "ranking",
		component: RankingView,
	},
	{
		path: "/team",
		name: "team",
		component: TeamView,
	},

]

const router = createRouter({
	history: createWebHistory(),
	routes,
})

router.beforeEach((to, from, next) => {
	if (to.name !== "login" && !localStorage.token) next({ name: "login" })
	else next()
})
router.beforeEach((to, from, next) => {
	if (to.name == "login" && localStorage.token) next({ name: "menu" })
	else next()
})

export default router
