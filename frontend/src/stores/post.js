import { defineStore } from 'pinia'
import { ref } from 'vue'
import { api } from 'boot/axios'

export const usePostStore = defineStore('post', () => {
  const current = ref(null)
  const comments = ref([])
  const loading = ref(false)
  const error = ref(null)

  async function fetch(id) {
    loading.value = true
    error.value = null
    current.value = null
    comments.value = []
    try {
      const { data } = await api.get(`/posts/${id}`)
      current.value = data.data || data.post || data
      const c = await api.get(`/posts/${id}/comments`)
      const list = Array.isArray(c.data) ? c.data : (c.data.data || [])
      comments.value = list
    } catch (e) {
      error.value = e
      throw e
    } finally {
      loading.value = false
    }
  }

  async function addComment(id, content) {
    const { data } = await api.post(`/posts/${id}/comments`, { content })
    const created = data.data || data
    comments.value.push(created)
    if (current.value) {
      current.value.comments_count = (current.value.comments_count || 0) + 1
    }
    return created
  }

  async function toggleLike(id) {
    if (!current.value || current.value.id !== id) return
    const prev = !!current.value.liked
    const prevCount = current.value.likes_count || 0
    current.value.liked = !prev
    current.value.likes_count = prevCount + (prev ? -1 : 1)
    try {
      await api.post(`/posts/${id}/like`)
    } catch (e) {
      current.value.liked = prev
      current.value.likes_count = prevCount
      throw e
    }
  }

  return { current, comments, loading, error, fetch, addComment, toggleLike }
})
