<template>
	<div class="flex h-full">
		<div class="absolut z-30 md:z-0 md:inline md:w-1/4">
			<DashboardMenu />
		</div>
		<div class="w-full md:w-3/4 pl-2">
			<router-view></router-view>
		</div>
	</div>
</template>

<script>
import DashboardMenu from "@/components/DashboardMenu.vue"
export default {
	components: { DashboardMenu },
}
</script>

<style></style>
