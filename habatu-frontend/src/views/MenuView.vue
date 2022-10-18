<template>
  <div class="grid grid-cols-2 md:grid-cols-5 gap-4">
    <div class="col-span-2 md:col-span-5 drop-shadow-lg bg-white rounded-md border p-3">
      <h1 v-if="nickname" class="text-2xl font-semibold">Hoi, {{nickname}} </h1>
    </div>
    <MenuItem name="Spielplan" to="table">
            <MapIcon class="h-full w-full" />
    </MenuItem>
    <MenuItem name="Reglä" to="rules">
            <BookIcon class="h-full w-full" />
    </MenuItem>
    <MenuItem name="Ranglischtä" to="ranking">
            <TrophyIcon class="h-full w-full" />
    </MenuItem>
    <MenuItem name="Grüchtli" to="rumours">
            <ChatHeartIcon class="h-full w-full" />
    </MenuItem>
    <MenuItem name="Nie ohne mein Team" to="teamtable">
            <PeopleIcon class="h-full w-full" />
    </MenuItem>
    <MenuItem name="Schiri" to="referee">
            <EyeglassesIcon class="h-full w-full" />
    </MenuItem>
    <MenuItem name="Stürig" to="dahsboard">
            <SpeedometerIcon class="h-full w-full" />
    </MenuItem>

  </div>
</template>

<script>
import MenuItem from '@/components/MenuItem.vue';
import MapIcon from '../components/icons/MapIcon.vue';
import TrophyIcon from '../components/icons/TrophyIcon.vue';
import SpeedometerIcon from '../components/icons/SpeedometerIcon.vue';
import ChatHeartIcon from '../components/icons/ChatHeartIcon.vue';
import PeopleIcon from '../components/icons/PeopleIcon.vue';
import BookIcon from '../components/icons/BookIcon.vue';
import EyeglassesIcon from '../components/icons/EyeglassesIcon.vue';
export default {
    components: { MenuItem, MapIcon, TrophyIcon, SpeedometerIcon, ChatHeartIcon, PeopleIcon, BookIcon, EyeglassesIcon },
    computed: {
      nickname(){
        return this.$store.state.user.user.nickname
      }
    }

}
</script>

<style>

</style>