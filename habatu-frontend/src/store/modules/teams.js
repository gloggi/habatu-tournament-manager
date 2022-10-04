import { mixin } from "@/mixins"

export const teams = {
    namespaced: true,
    state() {
        return {
            teams: undefined
        }
    },
    mutations: {
        saveTeams(state, teams) {
            state.teams = teams
        }
    },
    actions: {
        async create({ dispatch }, item) {
            try {
                await mixin.methods.callApi("post", "/teams", item)
                dispatch("get")
            } catch (e) {
                console.log(e)
            }
        },
        async get({ commit }) {
            try {
                const teams = await mixin.methods.callApi("get", "/teams")
                commit("saveTeams", teams.data)
            } catch (e) {
                console.log(e)
            }
        },
        async delete({ dispatch }, id) {
            try {
                await mixin.methods.callApi("delete", `/teams/${id}`)
                dispatch("get")
            } catch (e) {
                console.log(e)
            }
        },
        async update({ dispatch }, team) {
            try {
                await mixin.methods.callApi("put", `/teams/${team._id}`,team)
                dispatch("get")
            } catch (e) {
                console.log(e)
            }
        }
    },
    getters: {}
}