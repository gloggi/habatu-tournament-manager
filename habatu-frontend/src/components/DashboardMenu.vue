<template>
	<div class="fixed w-1/4">
		<div class="-m-2 mr-0 flex h-screen flex-col rounded-r-md bg-gray-900">
			<div class="flex h-1/4 items-center justify-center">
				<h1 class="text-4xl font-bold text-white">Dashboard</h1>
			</div>
			<div class="flex h-full flex-col">
				<DashboardMenuItem title="Teams" to="teamsDashboard"
					><PeopleIcon
				/></DashboardMenuItem>
				<DashboardMenuItem title="Abteilige" to="sectionsDashboard"
					><GlobeIcon
				/></DashboardMenuItem>
				<DashboardMenuItem title="Kategorie" to="categoriesDashboard"
					><TagsIcon
				/></DashboardMenuItem>
				<DashboardMenuItem title="Halle" to="hallsDashboard"
					><BuildingIcon
				/></DashboardMenuItem>
				<DashboardMenuItem title="Benutzer" to="usersDashboard"
					><PersonCircleIcon
				/></DashboardMenuItem>
				<DashboardMenuItem title="Ihstellige" to="optionsDashboard"
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
	},
}
</script>

<style></style>
