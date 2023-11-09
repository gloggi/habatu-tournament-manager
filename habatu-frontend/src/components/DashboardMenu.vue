<template>
	<div :class="`fixed w-1/4 transform ${isSidebarOpen ? 'translate-x-0' : '-translate-x-full'} transition-transform duration-300 ease-in-out md:translate-x-0`">
		<button 
      @click="isSidebarOpen = !isSidebarOpen" 
      class="absolute bottom-0 h-16 w-16 -right-16 bg-gray-900 rounded-r-md  z-30 md:hidden text-white flex justify-center items-center"
    >
     <ThreeBars class="h-8 w-8"/>
    </button>
      <div class="-m-2 mr-0 flex h-screen flex-col rounded-r-md bg-gray-900">
			<div class="flex h-1/4 items-center justify-center">
				<h1 class="text-4xl font-bold text-white hidden md:block">Dashboard</h1>
			</div>
			<div class="flex h-full flex-col">
				<DashboardMenuItem title="Teams" to="teamsDashboard" @clicked="closeMenu" v-if="false"
					><PeopleIcon
				/></DashboardMenuItem>
				<DashboardMenuItem title="Abteilige" to="sectionsDashboard" @clicked="closeMenu" v-if="false"
					><GlobeIcon
				/></DashboardMenuItem>
				<DashboardMenuItem title="Kategorie" to="categoriesDashboard" @clicked="closeMenu" v-if="false"
					><TagsIcon
				/></DashboardMenuItem>
				<DashboardMenuItem title="Halle" to="hallsDashboard" @clicked="closeMenu" v-if="false"
					><BuildingIcon
				/></DashboardMenuItem>
				<DashboardMenuItem title="Benutzer" to="usersDashboard" @clicked="closeMenu"
					><PersonCircleIcon
				/></DashboardMenuItem>
				<DashboardMenuItem title="Ihstellige" to="optionsDashboard" @clicked="closeMenu"
					><GearIcon
				/></DashboardMenuItem>
			</div>
			<div class="h-1/6">
				<DashboardMenuItem title="Zrugg zum Menü" to="menu"
					><ListIcon
				/></DashboardMenuItem>
			</div>
		</div>
	</div>
</template>

<script>
import DashboardMenuItem from "./DashboardMenuItem.vue"
import PeopleIcon from "./icons/PeopleIcon.vue"
import GlobeIcon from "./icons/GlobeIcon.vue"
import TagsIcon from "./icons/TagsIcon.vue"
import BuildingIcon from "./icons/BuildingIcon.vue"
import PersonCircleIcon from "./icons/PersonCircleIcon.vue"
import GearIcon from "./icons/GearIcon.vue"
import ListIcon from "./icons/ListIcon.vue"
import ThreeBars from "./icons/ThreeBars.vue"
export default {
	components: {
    DashboardMenuItem,
    PeopleIcon,
    GlobeIcon,
    TagsIcon,
    BuildingIcon,
    PersonCircleIcon,
    GearIcon,
    ListIcon,
    ThreeBars
},
	data() {
    return {
      isSidebarOpen: false,
    };
  },
  methods: {
	closeMenu(){
		this.isSidebarOpen = false;
	}
  },
}
</script>

<style></style>
