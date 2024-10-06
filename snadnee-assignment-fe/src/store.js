import {createStore} from 'vuex'
import axios from "axios";
import VuexPersistence from 'vuex-persist'

const vuexLocal = new VuexPersistence({
    reducer: (state) => ({account: state.account}),
})

const store = createStore({
    plugins: [vuexLocal.plugin],
    state: {
        apiAddress: "http://localhost:8082/api/v1/",
        account: null,
        reservations: null,
        tables: null,
    },
    getters: {
        reservations: (state) => state.reservations,
        apiAddress: (state) => state.apiAddress,
        tables: (state) => state.tables,
        account: (state) => state.account,
        auth: (state) => {
            return {
                headers: {
                    'Authorization': 'Bearer ' + state.account.accessToken,
                    'Content-Type': 'application/json',
                    // 'accept': 'application/json',
                },
            };
        },
        hasValidToken: (state) => {
            if (state.account === null) {
                return false;
            }
            return state.account.accessToken !== undefined;

        }
    },
    mutations: {
        setAccount(state, account) {
            state.account = account;
        },
        setReservations(state, reservations) {
            state.reservations = reservations;
        },
    },
    actions: {
        // eslint-disable-next-line
        async me({commit}, data) {
            let url = this.getters.apiAddress + "auth";

            await axios.post(
                url,
                data,
                this.getters.auth
            )
                .then(() => {
                    // TODO add new customer
                });
        },
        login({commit}, {email, password}) {
            let url = this.getters.apiAddress + "auth/login"

            return new Promise((resolve, reject) => {
                axios.post(url, {
                    email,
                    password
                }).then((response) => {
                    commit('setAccount', response.data.data)
                    resolve(response);
                }).catch((error) => {
                    reject(error.response.data)
                });
            });
        },
        async logout({commit}) {
            let url = this.getters.apiAddress + "auth/logout"
            let auth = this.getters.auth;
            commit('setAccount', null);
            await axios.post(url, {}, auth);
        },
        // eslint-disable-next-line
        register({commit}, data) {
            let url = this.getters.apiAddress + "auth/register";

            return new Promise((resolve, reject) => {
                axios.post(
                    url,
                    data,
                ).then((response) => {
                    commit('setAccount', response.data.data)
                    resolve(response);
                }).catch((error) => {
                    reject(error.response.data)
                });
            });
        },
        // eslint-disable-next-line
        async getReservations({commit}) {
            let url = this.getters.apiAddress + "reservations";

            let retrievedData;
            try {
                const response = await axios.get(
                    url,
                    this.getters.auth
                );
                retrievedData = response.data;
            } catch (error) {
                console.log(error);
            }

            commit('setReservations', retrievedData.data)

            return this.getters.reservations;
        },
        // eslint-disable-next-line
        async createReservation({commit}, data) {
            let url = this.getters.apiAddress + "reservations";

            await axios.post(
                url,
                data.data,
                this.getters.auth
            );
        },
        // eslint-disable-next-line
        async cancelReservation({commit}, entry) {
            let url = this.getters.apiAddress + "reservations/" + entry.id;

            await axios.delete(
                url,
                this.getters.auth
            );
        },
        // eslint-disable-next-line
        async getTableAvailability({commit}, data) {
            let url = this.getters.apiAddress + `tables/date/${data.reservationDatetime}/length/${data.reservationLength}`;

            let retrievedData;
            try {
                const response = await axios.get(
                    url,
                    this.getters.auth
                );
                retrievedData = response.data;
            } catch (error) {
                console.log(error);
            }

            return retrievedData.data;
        },
    },
});

export default store;