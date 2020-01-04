import axios from "axios";

export default {
    state: {
        isAuthenticated: false,
        user: {},
    },
    mutations: {
        // CHECK_AUTH_MUTATION: state => {
        //     axios.get('http://127.0.0.1:8000/api/auth/check')
        //         .then(res => {
        //             state.isAuthenticated = res.data.isAuthenticated;
        //         })
        //         .catch(error => {
        //             console.error(error);
        //         })
        // }
    },
    getters: {
        getAuth: state => {
            return state.isAuthenticated;
        },
        getCurrentUser: state => {
            return state.user;
        },
    },
    actions: {
        refreshToken: context => {
            return new Promise((resolve, reject) => {

                fetch('http://127.0.0.1:8000/api/token/refresh', {
                    method: 'POST',
                })
                .then(response => {
                    resolve(response);
                })
                .catch(error => {
                    reject(error);
                });

            });
        },
        checkAuth: context => {
            return new Promise((resolve, reject) => {
                axios.get('http://127.0.0.1:8000/api/auth/check')
                .then(res => {
                    resolve(res);
                    context.state.isAuthenticated = res.data.isAuthenticated;
                })
                .catch(error => {
                    console.error(error);
                })

                // context.commit('CHECK_AUTH_MUTATION');
            });
        },
        getCurrentUser: context => {
            return new Promise((resolve, reject) => {
                axios.get('http://127.0.0.1:8000/api/auth/user')
                .then(res => {
                    resolve(res);
                    context.state.user = res.data;
                })
                .catch(error => {
                    console.error(error);
                })

                // context.commit('CHECK_AUTH_MUTATION');
            });
        },
        logout: context => {
            return new Promise((resolve, reject) => {
                axios.get('http://127.0.0.1:8000/api/logout')
                .then(res => {
                    resolve(res);
                })
                .catch(error => {
                    console.error(error);
                })
            });
        }
    }
}