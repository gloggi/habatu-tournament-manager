import { mixin } from "@/mixins"

export const options = {
    namespaced: true,
    state() {
        return {
            options: undefined
        }
    },
    mutations: {
        saveOptions(state, options) {
            state.options = options
        }
    },
    actions: {
        async get({ commit }) {
            try {
                const options = await mixin.methods.callApi("get", "/options")
                commit("saveOptions", options.data)
            } catch (e) {
                console.log(e)
            }
        },
        async delete({ dispatch }, id) {
            try {
                await mixin.methods.callApi("delete", `/options/${id}`)
                dispatch("get")
            } catch (e) {
                console.log(e)
            }
        },
        async update({ dispatch }, option) {
            try {
                await mixin.methods.callApi("put", `/options/${option._id}`,option)
                dispatch("get")
            } catch (e) {
                console.log(e)
            }
        }
    },
    getters: {}
}