import { defineStore } from 'pinia'
import { ref } from 'vue'
import { api } from 'boot/axios'

export const useSearchStore = defineStore('search', () => {
  const users = ref([])
  const posts = ref([])
  const lastQuery = ref({ users: '', posts: '' })

  async function searchUsers(q) {
    if (lastQuery.value.users === q) return
    const { data } = await api.get('/search', { params: { q, type: 'users' } })
    users.value = Array.isArray(data) ? data : (data.data || [])
    lastQuery.value.users = q
  }

  async function searchPosts(q) {
    if (lastQuery.value.posts === q) return
    const { data } = await api.get('/search', { params: { q, type: 'posts' } })
    posts.value = Array.isArray(data) ? data : (data.data || [])
    lastQuery.value.posts = q
  }

  function reset() {
    users.value = []
    posts.value = []
    lastQuery.value = { users: '', posts: '' }
  }

  return { users, posts, lastQuery, searchUsers, searchPosts, reset }
})
