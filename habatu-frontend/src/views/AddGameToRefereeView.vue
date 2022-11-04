<template>
	<div></div>
</template>

<script>
export default {
	async created() {
		const userId = this.$store.state.user.user._id
		const gameId = this.$route.params.id
		try {
			await this.callApi("post", "/tournament/referee", {
				gameId,
				userId,
			})
			await this.$store.dispatch("user/getMe", localStorage.token)
			this.$router.push({ name: "referee" })
		} catch (e) {
			console.log(e)
		}
	},
}
</script>

<style></style>
