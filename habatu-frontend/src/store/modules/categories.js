import { mixin } from "@/mixins"

export const categories = {
    namespaced: true,
    state() {
        return {
            categories: undefined
        }
    },
    mutations: {
        saveCategories(state, categories) {
            state.categories = categories
        }
    },
    actions: {
        async create({ dispatch }, item) {
            try {
                await mixin.methods.callApi("post", "/categories", item)
                dispatch("get")
            } catch (e) {
                console.log(e)
            }
        },
        async get({ commit }) {
            try {
                const categories = await mixin.methods.callApi("get", "/categories")
                commit("saveCategories", categories.data)
            } catch (e) {
                console.log(e)
            }
        },
        async delete({ dispatch }, id) {
            try {
                await mixin.methods.callApi("delete", `/categories/${id}`)
                dispatch("get")
            } catch (e) {
                console.log(e)
            }
        },
        async update({ dispatch }, category) {
            try {
                await mixin.methods.callApi("put", `/categories/${category._id}`,category)
                dispatch("get")
            } catch (e) {
                console.log(e)
            }
        }
    },
    getters: {}
}