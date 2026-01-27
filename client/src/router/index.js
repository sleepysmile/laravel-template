import { createRouter, createWebHistory } from 'vue-router'
import LoginView from '../views/LoginView.vue'
import ChatList from '../views/ChatList.vue'
import ChatDetail from '../views/ChatDetail.vue'

const router = createRouter({
  history: createWebHistory(import.meta.env.BASE_URL),
  routes: [
    {
      path: '/',
      name: 'login',
      component: LoginView,
    },
    {
      path: '/chats',
      name: 'chat-list',
      component: ChatList,
    },
    {
      path: '/chats/:id',
      name: 'chat-detail',
      component: ChatDetail,
    }
  ],
})

router.beforeEach((to) => {
  const token = localStorage.getItem('auth_token')

  if (!token && to.name !== 'login') {
    return { name: 'login', query: { redirect: to.fullPath } }
  }

  if (token && to.name === 'login') {
    const redirectPath = typeof to.query.redirect === 'string' ? to.query.redirect : '/chats'
    return { path: redirectPath }
  }
})

export default router
