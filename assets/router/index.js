import { createRouter, createWebHashHistory } from 'vue-router'
import { useCineplaceStore } from '../stores'

import Info from '../components/public/Info.vue'
import Home from '../components/public/Home.vue'
import VoirTout from '../components/public/VoirTout.vue'
import DetailsFilm from '../components/public/DetailsFilm.vue'

import Login from '../components/users/Login.vue'
import Register from '../components/users/Register.vue'

import HomeClient from '../components/users/Home.vue'
import UpdateClient from '../components/users/Update.vue'
import ReservationsClient from '../components/users/Reservations.vue'
import Reserver from '../components/users/Reserver.vue'

const routes = [
    {
      path: '/',
      name : 'home',
      component : Home
    },
    {
      path: '/info',
      name : 'info',
      component : Info
    },
    {
      path: '/voirTout',
      name : 'voirTout',
      component : VoirTout
    },
    {
      path: '/details/:idFilm',
      name: 'detailsFilm',
      component: DetailsFilm
    },
    {
      path: '/login',
      name: 'login',
      component: Login
    },
    {
      path: '/register',
      name: 'register',
      component: Register
    },
    {
      path: '/homeClient',
      name: 'homeClient',
      component: HomeClient
    },
    {
      path: '/updateClient',
      name: 'updateClient',
      component: UpdateClient
    },
    {
      path: '/reservationsClient',
      name: 'reservationsClient',
      component: ReservationsClient
    },
    {
      path: '/reserver',
      name: 'reserver',
      component: Reserver
    }
]

const router = createRouter({
    history: createWebHashHistory(),
    routes,
  })

export default router

//Restriction des pages clients au utilisateurs connectés
router.beforeEach((to, from, next) => {
  const store = useCineplaceStore()
  // on redirige sur la page login si on n'est pas authentifié...
  const publicPages = ['home', 'login', 'info', 'voirTout', 'detailsFilm', 'register'];
  const authRequired = !publicPages.includes(to.name);
  const loggedIn = store.token;

  if (authRequired && !loggedIn) {
    return next('/login');
  }
  next();
})
