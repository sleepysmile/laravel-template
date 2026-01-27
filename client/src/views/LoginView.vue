<script setup>
import { ref } from 'vue'
import { useRouter, useRoute } from 'vue-router'

const email = ref('')
const password = ref('')
const isSubmitting = ref(false)
const errorMessage = ref('')

const API_BASE_URL = import.meta.env.VITE_API_BASE_URL || 'http://localhost:8080'
const router = useRouter()
const route = useRoute()

const onSubmit = async () => {
  errorMessage.value = ''
  isSubmitting.value = true
  try {
    if (!email.value || !password.value) {
      throw new Error('Введите email и пароль')
    }

    const response = await fetch(`${API_BASE_URL}/api/signin`, {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json',
        'Accept': 'application/json',
      },
      body: JSON.stringify({
        email: email.value,
        password: password.value,
      }),
    })

    const data = await response.json().catch(() => null)

    if (!response.ok || !data?.success) {
      // Форматы ошибок:
      // - Validation: { success:false, details: { field: [msg] } }
      // - LogicException: { success:false, details: { error: '...' } }
      const validation = data?.details
      const firstValidationMsg = validation && typeof validation === 'object'
        ? Object.values(validation).flat().at(0)
        : null
      const logicMsg = validation?.error
      throw new Error(firstValidationMsg || logicMsg || 'Ошибка авторизации')
    }

    const token = data?.results?.token
    if (!token) {
      throw new Error('Токен не получен')
    }

    localStorage.setItem('auth_token', token)
    const target = typeof route.query.redirect === 'string' ? route.query.redirect : '/chats'
    router.replace(target)
  } catch (err) {
    errorMessage.value = err?.message || 'Ошибка входа'
  } finally {
    isSubmitting.value = false
  }
}
</script>

<template>
  <div class="container min-vh-100 d-flex align-items-center justify-content-center">
    <div class="w-100" style="max-width: 420px;">
      <div class="card shadow-sm">
        <div class="card-body p-4">
          <h1 class="h4 mb-4 text-center">Вход</h1>
          <form @submit.prevent="onSubmit" novalidate>
            <div class="mb-3">
              <label for="email" class="form-label">Email</label>
              <input
                id="email"
                v-model="email"
                type="email"
                class="form-control"
                placeholder="you@example.com"
                required
                autocomplete="username"
              >
            </div>
            <div class="mb-3">
              <label for="password" class="form-label">Пароль</label>
              <input
                id="password"
                v-model="password"
                type="password"
                class="form-control"
                placeholder="••••••••"
                required
                autocomplete="current-password"
              >
            </div>
            <div v-if="errorMessage" class="alert alert-danger py-2" role="alert">
              {{ errorMessage }}
            </div>
            <button type="submit" class="btn btn-primary w-100" :disabled="isSubmitting">
              <span
                v-if="isSubmitting"
                class="spinner-border spinner-border-sm me-2"
                role="status"
                aria-hidden="true"
              ></span>
              Войти
            </button>
          </form>
        </div>
      </div>
    </div>
  </div>
</template>

