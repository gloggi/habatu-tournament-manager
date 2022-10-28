const { defineConfig } = require("@vue/cli-service")
module.exports = defineConfig({
	transpileDependencies: true,
	pwa: {
		name: 'HaBaTu',
		themeColor: "#ff0000",
	}
})
