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
import CreateHalls from "../views/CreateHalls"
import DashboardView from "../views/DashboardView"
import UserDashboard from "../views/UserDashboard"
import HallDashboard from "../views/HallDashboard"
import SectionDashboard from "../views/SectionDashboard"
import TeamDashboard from "../views/TeamDashboard"
import CategoryDashboard from "../views/CategoryDashboard"
import OptionDashboard from "../views/OptionDashboard"
import MeView from "../views/MeView"
import UserDashboardEdit from "../views/UserDashboardEdit"

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
		path: "/halls",
		name: "halls",
		component: CreateHalls,
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
	{
		path: "/me",
		name: "me",
		component: MeView,
	},
	{
		path: "/dashboard",
		name: "dashboard",
		component: DashboardView,
		children: [
			{
				path: "users",
				name: "usersDashboard",
				component: UserDashboard,
			},
			{
				path: "users/:id",
				name: "usersEdit",
				component: UserDashboardEdit,
			},
			{
				path: "halls",
				name: "hallsDashboard",
				component: HallDashboard,
			},
			{
				path: "sections",
				name: "sectionsDashboard",
				component: SectionDashboard,
			},
			{
				path: "teams",
				name: "teamsDashboard",
				component: TeamDashboard,
			},
			{
				path: "categories",
				name: "categoriesDashboard",
				component: CategoryDashboard,
			},
			{
				path: "options",
				name: "optionsDashboard",
				component: OptionDashboard,
			},
		],
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
