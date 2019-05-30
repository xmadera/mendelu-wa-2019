import Vue from 'vue';
import Router from 'vue-router';
import Rooms from '@/views/Rooms';
import Login from '@/views/Login';
import Register from '@/views/Register';
import AuthSection from '@/views/AuthSection';
import Room from '@/views/Room';
import Homepage from '@/views/Homepage';

Vue.use(Router);

export default new Router({
    routes: [
        {
            path: '/',
            name: 'homepage',
            component: Homepage
        },
        {
            path: '/login',
            name: 'login',
            component: Login
        },
        {
            path: '/registrace',
            name: 'register',
            component: Register
        },

        // zabezpecena sekce, vyzaduje prihlaseni
        {
            path: '/auth',
            name: 'auth',
            component: AuthSection,
            beforeEnter: requireAuth,
            children: [
                {
                    path: '/rooms',
                    name: 'rooms',
                    component: Rooms
                },
                {
                    path: '/rooms/:id',
                    name: 'room',
                    component: Room
                }
            ]
        },
    ]
});

function requireAuth(to, from, next) {
    const token = localStorage.getItem('token');

    if (token === null || token === undefined) {
        next({name: 'login', params: {nextUrl: to.fullPath}});
    } else {
        next();
    }
}
