import { createRouter, createWebHistory } from 'vue-router'
import login from '../components/login.vue'
import miHorario from '../components/miHorario.vue'
import misFaltas from '../components/misFaltas.vue'
import faltasHoy from '../components/faltasHoy.vue'
import horarioProfesorado from '../components/horarioProfesorado.vue'
import plantillaDocente from '../components/plantillaDocente.vue'

// Define your routes
const routes = [
  {
    path: '/',
    name: 'login',
    component: login
  },
  {
    path: '/horario',
    name: 'horario',
    component: miHorario,
    meta: { requiresAuth: true }
  },
  {
    path: '/misFaltas',
    name: 'misFaltas',
    component: misFaltas,
    meta: { requiresAuth: true }
  },
  {
    path: '/faltasHoy',
    name: 'faltasHoy',
    component: faltasHoy,
    meta: { requiresAuth: true }
  },
  {
    path: '/horarioProfesorado',
    name: 'horarioProfesorado',
    component: horarioProfesorado,
    meta: { requiresAuth: true }
  },
  {
    path: '/plantillaDocente',
    name: 'plantillaDocente',
    component: plantillaDocente,
    meta: { requiresAuth: true }
  }
]

const router = createRouter({
  history: createWebHistory(import.meta.env.BASE_URL),
  routes
})

router.beforeEach((to, from, next) => {
  const isLoggedIn = !!localStorage.getItem('access_token');
  if (to.matched.some(record => record.meta.requiresAuth) && !isLoggedIn) {
    next({ name: 'login' });
  } else {
    next();
  }
});

export default router;
