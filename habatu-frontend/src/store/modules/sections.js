import { mixin } from "@/mixins"

export const sections = {
    namespaced: true,
    state() {
        return {
            sections: undefined
        }
    },
    mutations: {
        saveSections(state, sections) {
            state.sections = sections
        }
    },
    actions: {
        async create({ dispatch }, item) {
            try {
                await mixin.methods.callApi("post", "/sections", item)
                dispatch("get")
            } catch (e) {
                console.log(e)
            }
        },
        async get({ commit }) {
            try {
                const sections = await mixin.methods.callApi("get", "/sections")
                commit("saveSections", sections.data)
            } catch (e) {
                console.log(e)
            }
        },
        async delete({ dispatch }, id) {
            try {
                await mixin.methods.callApi("delete", `/sections/${id}`)
                dispatch("get")
            } catch (e) {
                console.log(e)
            }
        },
        async update({ dispatch }, section) {
            try {
                await mixin.methods.callApi("put", `/sections/${section._id}`,section)
                dispatch("get")
            } catch (e) {
                console.log(e)
            }
        }
    },
    getters: {}
}