import { defineStore } from 'pinia'
import { ref } from 'vue'
import { api } from 'boot/axios'

export const useProfileStore = defineStore('profile', () => {
  const profiles = ref(new Map())
  const loading = ref(false)
  const error = ref(null)

  function getProfile(username) {
    return profiles.value.get(username) || null
  }

  function setProfile(username, data) {
    profiles.value.set(username, data)
  }

  async function fetchProfile(username) {
    loading.value = true
    error.value = null
    try {
      const { data } = await api.get(`/users/${username}`)
      const payload = data.data || data.user || data
      setProfile(username, payload)
      return payload
    } catch (e) {
      error.value = e
      throw e
    } finally {
      loading.value = false
    }
  }

  async function fetchUserPosts(username) {
    const { data } = await api.get(`/users/${username}/posts`)
    return Array.isArray(data) ? data : (data.data || [])
  }

  async function fetchFollowers(username) {
    const { data } = await api.get(`/users/${username}/followers`)
    return Array.isArray(data) ? data : (data.data || [])
  }

  async function fetchFollowing(username) {
    const { data } = await api.get(`/users/${username}/following`)
    return Array.isArray(data) ? data : (data.data || [])
  }

  async function toggleFollow(username) {
    const current = getProfile(username)
    if (!current) return
    const prev = !!current.is_following
    const prevCount = current.followers_count || 0
    current.is_following = !prev
    current.followers_count = prevCount + (prev ? -1 : 1)
    try {
      await api.post(`/users/${username}/follow`)
    } catch (e) {
      current.is_following = prev
      current.followers_count = prevCount
      throw e
    }
  }

  async function toggleLikeOnList(list, postId) {
    const target = list.find((p) => p.id === postId)
    if (!target) return
    const prev = !!target.liked
    const prevCount = target.likes_count || 0
    target.liked = !prev
    target.likes_count = prevCount + (prev ? -1 : 1)
    try {
      await api.post(`/posts/${postId}/like`)
    } catch (e) {
      target.liked = prev
      target.likes_count = prevCount
      throw e
    }
  }

  async function updateMe(payload) {
    const { data } = await api.put('/me', payload)
    return data
  }

  return {
    profiles,
    loading,
    error,
    getProfile,
    fetchProfile,
    fetchUserPosts,
    fetchFollowers,
    fetchFollowing,
    toggleFollow,
    toggleLikeOnList,
    updateMe
  }
})
