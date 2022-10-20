<template>
    <div class="fixed w-1/4">
  <div class="-m-2 mr-0 flex flex-col h-screen rounded-r-md bg-gray-900">
    <div class="h-1/4 flex justify-center items-center">
        <h1 class="text-4xl font-bold text-white">
            Dashboard
        </h1>
    </div>
    <div class="flex flex-col h-full">
    <DashboardMenuItem title="Teams" to="teamsDashboard"><PeopleIcon/></DashboardMenuItem>
    <DashboardMenuItem title="Abteilungen" to="sectionsDashboard"><GlobeIcon/></DashboardMenuItem>
    <DashboardMenuItem title="Kategorien" to="categoriesDashboard"><TagsIcon/></DashboardMenuItem>
    <DashboardMenuItem title="Hallen" to="hallsDashboard"><BuildingIcon/></DashboardMenuItem>
    <DashboardMenuItem title="Benutzer" to="usersDashboard"><PersonCircleIcon/></DashboardMenuItem>
    <DashboardMenuItem title="Optionen" to="optionsDashboard"><GearIcon/></DashboardMenuItem>
  </div>
  <div class="h-1/6">
  <DashboardMenuItem title="Back to the Menu" to="menu"><ListIcon/></DashboardMenuItem>
</div>
  </div>
</div>
</template>

<script>
import DashboardMenuItem from './DashboardMenuItem.vue';
import PeopleIcon from './icons/PeopleIcon.vue';
import GlobeIcon from './icons/GlobeIcon.vue';
import TagsIcon from './icons/TagsIcon.vue';
import BuildingIcon from './icons/BuildingIcon.vue';
import PersonCircleIcon from './icons/PersonCircleIcon.vue';
import GearIcon from './icons/GearIcon.vue';
import ListIcon from './icons/ListIcon.vue';
export default {
    components: { DashboardMenuItem, PeopleIcon, GlobeIcon, TagsIcon, BuildingIcon, PersonCircleIcon, GearIcon, ListIcon }
}
</script>

<style>

</style>