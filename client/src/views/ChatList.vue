<script setup>
import { ref, onMounted } from 'vue'
import { RouterLink } from 'vue-router'

const users = ref([])
const isLoading = ref(false)
const errorMessage = ref('')

const API_BASE_URL = import.meta.env.VITE_API_BASE_URL || 'http://localhost:8080'

const loadUsers = async () => {
  errorMessage.value = ''
  isLoading.value = true
  try {
    const token = localStorage.getItem('auth_token')
    if (!token) {
      throw new Error('Нет токена авторизации')
    }
    const response = await fetch(`${API_BASE_URL}/api/users`, {
      method: 'GET',
      headers: {
        'Accept': 'application/json',
        'Authorization': `Bearer ${token}`,
      },
    })
    const data = await response.json().catch(() => null)
    if (!response.ok || !data?.success) {
      const details = data?.details
      const error = (typeof details === 'object' && details?.error) || 'Не удалось загрузить пользователей'
      throw new Error(error)
    }
    users.value = Array.isArray(data?.results) ? data.results : []
  } catch (err) {
    errorMessage.value = err?.message || 'Ошибка загрузки'
  } finally {
    isLoading.value = false
  }
}

onMounted(loadUsers)
</script>

<template>
  <div class="container py-4">
    <div class="row justify-content-center">
      <div class="col-12 col-md-8 col-lg-6">
        <div class="d-flex align-items-center justify-content-between mb-3">
          <h1 class="h4 mb-0">Пользователи</h1>
        </div>
        <div v-if="errorMessage" class="alert alert-danger" role="alert">{{ errorMessage }}</div>
        <div v-else-if="isLoading" class="text-center py-4">
          <div class="spinner-border text-primary" role="status" aria-hidden="true"></div>
        </div>
        <template v-else>
          <div v-if="users.length === 0" class="text-center text-muted py-4">Пользователи не найдены</div>
          <div v-else class="list-group shadow-sm">
            <RouterLink
              v-for="user in users"
              :key="user.id"
              :to="{ name: 'chat-detail', params: { id: user.id } }"
              class="list-group-item list-group-item-action d-flex justify-content-between align-items-center"
            >
              <div class="fw-semibold">{{ user.name }}({{ user.email }})</div>
              <span class="badge text-bg-primary rounded-pill">{{ user.id }}</span>
            </RouterLink>
          </div>
        </template>
      </div>
    </div>
  </div>
</template>

