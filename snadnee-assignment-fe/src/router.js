import {createRouter, createWebHistory} from 'vue-router'

import ReservationScreen from "@/components/reservations/ReservationScreen.vue";

const router = createRouter({
    history: createWebHistory(),
    routes: [
        {
            path: '/',
            component: ReservationScreen
        },
    ]
});

export default router;