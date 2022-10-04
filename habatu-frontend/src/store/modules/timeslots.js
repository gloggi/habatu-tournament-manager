import { mixin } from "@/mixins"

export const timeslots = {
    namespaced: true,
    state() {
        return {
            timeslots: undefined
        }
    },
    mutations: {
        saveTimeslots(state, timeslots) {
            state.timeslots = timeslots
        }
    },
    actions: {
        async get({ commit }) {
            try {
                const timeslots = await mixin.methods.callApi("get", "/timeslots")
                commit("saveTimeslots", timeslots.data)
            } catch (e) {
                console.log(e)
            }
        },
        async delete({ dispatch }, id) {
            try {
                await mixin.methods.callApi("delete", `/timeslots/${id}`)
                dispatch("get")
            } catch (e) {
                console.log(e)
            }
        },
        async update({ dispatch }, timeslot) {
            try {
                await mixin.methods.callApi("put", `/timeslots/${timeslot._id}`,timeslot)
                dispatch("get")
            } catch (e) {
                console.log(e)
            }
        }
    },
    getters: {}
}