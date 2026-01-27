<script setup>
import { ref, computed, onMounted, onBeforeUnmount } from 'vue'
import { useRoute, useRouter } from 'vue-router'

const route = useRoute()
const router = useRouter()

const API_BASE_URL = import.meta.env.VITE_API_BASE_URL || 'http://localhost:8080'

// route.params.id — это id собеседника; чат инициализируем через /api/chat/init
const peerUserId = computed(() => Number(route.params.id))

const chatId = ref(null)
const messages = ref([])
const isLoading = ref(false)
const isSending = ref(false)
const errorMessage = ref('')
const newMessage = ref('')

const page = ref(1)
const perPage = ref(50)
const lastMessageId = ref(null)

let pollingTimer = null

const authToken = () => localStorage.getItem('auth_token') || ''

const goBack = () => {
  router.push('/chats')
}

const apiGet = async (url) => {
  const response = await fetch(url, {
    method: 'GET',
    headers: {
      'Accept': 'application/json',
      'Authorization': `Bearer ${authToken()}`,
    },
  })
  const data = await response.json().catch(() => null)
  if (!response.ok || !data?.success) {
    const details = data?.details
    const err = (typeof details === 'object' && (details.error || Object.values(details).flat().at(0))) || 'Ошибка запроса'
    throw new Error(err)
  }
  return data
}

const apiPost = async (url, body) => {
  const response = await fetch(url, {
    method: 'POST',
    headers: {
      'Content-Type': 'application/json',
      'Accept': 'application/json',
      'Authorization': `Bearer ${authToken()}`,
    },
    body: JSON.stringify(body),
  })
  const data = await response.json().catch(() => null)
  if (!response.ok || !data?.success) {
    const details = data?.details
    const err = (typeof details === 'object' && (details.error || Object.values(details).flat().at(0))) || 'Ошибка запроса'
    throw new Error(err)
  }
  return data
}

const ensureChat = async () => {
  const res = await apiPost(`${API_BASE_URL}/api/chat/init`, { user_id: peerUserId.value })
  const id = res?.results?.id
  if (!id) throw new Error('Не удалось инициализировать чат')
  chatId.value = id
}

const normalizeList = (payload) => {
  // Коллекция может прийти как results (array) или results.data (paginator)
  const results = payload?.results
  if (Array.isArray(results)) return results
  if (results && Array.isArray(results.data)) return results.data
  return []
}

const loadInitialMessages = async () => {
  const url = new URL(`${API_BASE_URL}/api/chat/message/find`)
  url.searchParams.set('chat_id', String(chatId.value))
  url.searchParams.set('page', String(page.value))
  url.searchParams.set('per_page', String(perPage.value))
  url.searchParams.set('order', 'asc')

  const data = await apiGet(url.toString())
  const list = normalizeList(data)
  messages.value = list
  lastMessageId.value = list.length ? list[list.length - 1].id : null
}

const pollNewMessages = async () => {
  if (!chatId.value) return
  try {
    const url = new URL(`${API_BASE_URL}/api/chat/message/pooling`)
    url.searchParams.set('chat_id', String(chatId.value))
    if (lastMessageId.value != null) {
      url.searchParams.set('last_id', String(lastMessageId.value))
    }
    url.searchParams.set('order', 'asc')

    const data = await apiGet(url.toString())
    const list = normalizeList(data)
    if (list.length > 0) {
      messages.value = messages.value.concat(list)
      lastMessageId.value = list[list.length - 1].id
      // автопрокрутка вниз
      requestAnimationFrame(() => {
        const box = document.getElementById('messagesBox')
        if (box) box.scrollTop = box.scrollHeight
      })
    }
  } catch (e) {
    // мягко игнорируем ошибки pooling, чтобы не рвать цикл
  }
}

const startPolling = () => {
  stopPolling()
  pollingTimer = setInterval(pollNewMessages, 1500)
}

const stopPolling = () => {
  if (pollingTimer) {
    clearInterval(pollingTimer)
    pollingTimer = null
  }
}

const sendMessage = async () => {
  if (!newMessage.value.trim() || !chatId.value) return
  isSending.value = true
  errorMessage.value = ''
  try {
    const res = await apiPost(`${API_BASE_URL}/api/chat/message/create`, {
      chat_id: chatId.value,
      body: newMessage.value.trim(),
    })
    const msg = res?.results
    if (msg?.id) {
      messages.value.push(msg)
      lastMessageId.value = msg.id
      newMessage.value = ''
      requestAnimationFrame(() => {
        const box = document.getElementById('messagesBox')
        if (box) box.scrollTop = box.scrollHeight
      })
    }
  } catch (err) {
    errorMessage.value = err?.message || 'Не удалось отправить сообщение'
  } finally {
    isSending.value = false
  }
}

onMounted(async () => {
  try {
    isLoading.value = true
    await ensureChat()
    await loadInitialMessages()
    startPolling()
  } catch (err) {
    errorMessage.value = err?.message || 'Ошибка загрузки чата'
  } finally {
    isLoading.value = false
  }
})

onBeforeUnmount(() => {
  stopPolling()
})

</script>

<template>
  <div class="container py-4">
    <div class="row justify-content-center">
      <div class="col-12 col-md-8 col-lg-6">
        <div class="d-flex align-items-center justify-content-between mb-3">
          <button type="button" class="btn btn-outline-secondary btn-sm" @click="goBack">
            ← Назад к списку
          </button>
          <h1 class="h5 mb-0">Чат с пользователем #{{ peerUserId }}</h1>
        </div>

        <div class="card shadow-sm">
          <div class="card-body" id="messagesBox" style="min-height: 300px; max-height: 50vh; overflow-y: auto;">
            <div v-if="errorMessage" class="alert alert-danger py-2 mb-2" role="alert">
              {{ errorMessage }}
            </div>
            <div v-else-if="isLoading" class="text-center py-4">
              <div class="spinner-border text-primary" role="status" aria-hidden="true"></div>
            </div>
            <template v-else>
              <div v-if="messages.length === 0" class="text-muted text-center">Сообщений пока нет</div>
              <ul v-else class="list-unstyled mb-0">
                <li v-for="m in messages" :key="m.id" class="mb-2">
                  <div class="p-2 rounded border">
                    <div class="small text-muted mb-1">#{{ m.id }} • от {{ m.sender_id }}</div>
                    <div class="fw-semibold">{{ m.body }}</div>
                  </div>
                </li>
              </ul>
            </template>
          </div>
          <div class="card-footer bg-white">
            <form class="d-flex gap-2" @submit.prevent="sendMessage">
              <input
                v-model="newMessage"
                type="text"
                class="form-control"
                placeholder="Введите сообщение..."
                :disabled="!chatId || isSending"
              />
              <button type="submit" class="btn btn-primary" :disabled="!chatId || isSending || !newMessage.trim()">
                <span v-if="isSending" class="spinner-border spinner-border-sm me-2" role="status" aria-hidden="true"></span>
                Отправить
              </button>
            </form>
          </div>
        </div>
      </div>
    </div>
  </div>
  </template>

