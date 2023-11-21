<template>
	<DashboardTable state="users" name="Users" keys="nickname,role,team" />
</template>

<script>
import DashboardTable from "@/components/DashboardTable.vue"
export default {
	components: { DashboardTable },
}
</script>

<style></style>
