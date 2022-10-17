
export const notifications = {
    namespaced: true,
    state() {
        return {
            show: false,
            message: undefined
        }
    },
    mutations: {
        showNotification(state, message) {
            state.show = true
            state.message = message
            setTimeout(()=>{
                state.show = false
                state.message = undefined
            },2000)
        }
    },
    actions: {},
    getters: {}
}