import { createRouter, createWebHistory } from 'vue-router'
import { useAuthStore } from '../stores/auth'
import Dashboard from '../views/Dashboard.vue'
import Login from '../views/Login.vue'
import Users from '../views/Users.vue'
import UserForm from '../views/UserForm.vue'
import Equipment from '../views/Equipment.vue'
import EquipmentForm from '../views/EquipmentForm.vue'
import Assignments from '../views/Assignments.vue'
import Companies from '../views/Companies.vue'
import Roles from '../views/Roles.vue'
import Audit from '../views/Audit.vue'
import Departments from '../views/Departments.vue'
import Onboarding from '../views/Onboarding.vue'

const routes = [
  { path: '/login', name: 'Login', component: Login, meta: { guest: true } },
  { path: '/', redirect: '/dashboard' },
  {
    path: '/dashboard',
    name: 'Dashboard',
    component: Dashboard,
    meta: { requiresAuth: true },
  },
  {
    path: '/companies',
    name: 'Companies',
    component: Companies,
    meta: { requiresAuth: true },
  },
  {
    path: '/users',
    name: 'Users',
    component: Users,
    meta: { requiresAuth: true },
  },
  {
    path: '/users/new',
    redirect: '/users',
  },
  {
    path: '/users/:id/edit',
    name: 'UserEdit',
    component: UserForm,
    meta: { requiresAuth: true },
  },
  {
    path: '/departments',
    name: 'Departments',
    component: Departments,
    meta: { requiresAuth: true },
  },
  {
    path: '/equipment',
    name: 'Equipment',
    component: Equipment,
    meta: { requiresAuth: true },
  },
  {
    path: '/equipment/new',
    redirect: '/equipment',
  },
  {
    path: '/equipment/:id/edit',
    name: 'EquipmentEdit',
    component: EquipmentForm,
    meta: { requiresAuth: true },
  },
  {
    path: '/assignments',
    name: 'Assignments',
    component: Assignments,
    meta: { requiresAuth: true },
  },
  {
    path: '/roles',
    name: 'Roles',
    component: Roles,
    meta: { requiresAuth: true },
  },
  {
    path: '/audit',
    name: 'Audit',
    component: Audit,
    meta: { requiresAuth: true },
  },
  {
    path: '/onboarding',
    name: 'Onboarding',
    component: Onboarding,
    meta: { requiresAuth: true },
  },
]

const router = createRouter({
  history: createWebHistory(),
  routes,
})

router.beforeEach((to, from, next) => {
  const authStore = useAuthStore()
  if (to.meta.requiresAuth && !authStore.isAuthenticated) {
    next('/login')
  } else if (to.meta.guest && authStore.isAuthenticated) {
    next('/dashboard')
  } else {
    next()
  }
})

export default router
