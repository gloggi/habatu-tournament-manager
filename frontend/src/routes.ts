import { createWebHistory, createRouter } from "vue-router";
import { useUserStore } from "@/stores/user";

import Home from "./views/Home.vue";
import Create from "./views/Create.vue";
import GameTable from "./views/GameTable.vue";
import Admin from "./views/Admin.vue";
import Halls from "./views/admin/Halls.vue";
import Hall from "./views/admin/Hall.vue";
import Categories from "./views/admin/Categories.vue";
import Category from "./views/admin/Category.vue";
import Section from "./views/admin/Section.vue";
import Sections from "./views/admin/Sections.vue";
import Team from "./views/admin/Team.vue";
import Teams from "./views/admin/Teams.vue";
import TournamentSettings from "./views/admin/TournamentSettings.vue";
import Login from "./views/Login.vue";
import User from "./views/admin/User.vue";
import Users from "./views/admin/Users.vue";
import Ranking from "./views/Ranking.vue";
import Profile from "./views/Profile.vue";
import Referee from "./views/Referee.vue";
import MyTeamTable from "./views/MyTeamTable.vue";
import WhistleView from "./views/WhistleView.vue";
import Messages from "./views/Messages.vue";
import { RouteRecordRaw } from "vue-router";

const routes : Array<RouteRecordRaw> = [
  { path: "/", name: "Home", component: Home },
  {
    path: "/setup",
    component: Create,
    name: "Setup",
    children: [{ path: ":entity", name: "Create", component: Create }],
  },
  { path: "/table", name: "GameTable", component: GameTable },
  { path: "/ranking", name: "Ranking", component: Ranking },
  { path: "/profile", name: "Profile", component: Profile },
  { path: "/referee", name: "Referee", component: Referee },
  { path: "/my-team", name: "MyTeam", component: MyTeamTable },
  { path: "/whistle/:id", name: "Whistle", component: WhistleView },
  { path: "/messages", name: "Messages", component: Messages },
  {
    path: "/admin",
    name: "Admin",
    component: Admin,
    children: [
      { path: "halls", name: "Halls", component: Halls },
      { path: "halls/:id", name: "Hall", component: Hall },
      { path: "categories", name: "Categories", component: Categories },
      { path: "categories/:id", name: "Category", component: Category },
      { path: "sections", name: "Sections", component: Sections },
      { path: "sections/:id", name: "Section", component: Section },
      { path: "teams", name: "Teams", component: Teams },
      { path: "teams/:id", name: "Team", component: Team },
      { path: "users", name: "Users", component: Users },
      { path: "users/:id", name: "User", component: User },
      {
        path: "settings",
        name: "TournamentSettings",
        component: TournamentSettings,
      },
    ],
  },
  { path: "/login", name: "Login", component: Login },
];

export const router = createRouter({
  history: createWebHistory(),
  routes,
});

const isAuthenticated = (): boolean => {
  const token = localStorage.getItem("token");
  return !!token;
};

const adminOnlyRoutes = [
  "Admin",
  "Halls", 
  "Hall",
  "Categories",
  "Category",
  "Sections", 
  "Section",
  "Teams",
  "Team",
  "Users",
  "User",
  "TournamentSettings"
];

const refereeOnlyRoutes = [
  "Whistle",
  "Referee"
];


router.beforeEach(async (to, _, next) => {
  if (to.name !== "Login" && !isAuthenticated()) {
    next({ name: "Login" });
    return;
  }


  const userStore = useUserStore();


  if (isAuthenticated()) {
    await userStore.fetchUser();
  }


  if (to.name && adminOnlyRoutes.includes(to.name as string)) {
    if (!userStore.isAdmin) {
      next({ name: "Home" });
      return;
    }
  }


  if (to.name && refereeOnlyRoutes.includes(to.name as string)) {
    if (!userStore.isReferee) {
      next({ name: "Home" });
      return;
    }
  }

  next();
});
