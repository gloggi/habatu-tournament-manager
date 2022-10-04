import { createRouter, createWebHistory } from 'vue-router'
import HomeView from '../views/HomeView.vue'
import LoginView from '../views/LoginView.vue'
import TestView from '../views/TestView'
import CreateView from '../views/CreateView'
import CreateSections from '../views/CreateSections'
import CreateCategories from '../views/CreateCategories'
import CreateTeams from '../views/CreateTeams'

const routes = [
  {
    path: '/',
    name: 'home',
    component: HomeView
  },
  {
    path: '/login',
    name: 'login',
    component: LoginView
  },
  {
    path: '/test',
    name: 'test',
    component: TestView
  },
  {
    path: '/create',
    name: 'create',
    component: CreateView
  },
  {
    path: '/sections',
    name: 'sections',
    component: CreateSections
  },
  {
    path: '/categories',
    name: 'categories',
    component: CreateCategories
  },
  {
    path: '/teams',
    name: 'teams',
    component: CreateTeams
  }
]

const router = createRouter({
  history: createWebHistory(),
  routes
})

export default router
