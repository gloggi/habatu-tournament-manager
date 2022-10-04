import { mixin } from "@/mixins"

export const games = {
    namespaced: true,
    state() {
        return {
            games: undefined
        }
    },
    mutations: {
        saveGames(state, games) {
            state.games = games
        }
    },
    actions: {
        async create({ dispatch }, item) {
            try {
                await mixin.methods.callApi("post", "/games", item)
                dispatch("get")
            } catch (e) {
                console.log(e)
            }
        },
        async get({ commit }) {
            try {
                const games = await mixin.methods.callApi("get", "/games")
                commit("saveGames", games.data)
            } catch (e) {
                console.log(e)
            }
        },
        async delete({ dispatch }, id) {
            try {
                await mixin.methods.callApi("delete", `/games/${id}`)
                dispatch("get")
            } catch (e) {
                console.log(e)
            }
        },
        async update({ dispatch }, game) {
            try {
                await mixin.methods.callApi("put", `/games/${game._id}`,game)
                dispatch("get")
            } catch (e) {
                console.log(e)
            }
        }
    },
    getters: {}
}