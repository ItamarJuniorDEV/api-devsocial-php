<template>
  <q-avatar :size="size" color="primary" text-color="dark">
    <img v-if="user?.avatar_url" :src="user.avatar_url" :alt="user?.name" />
    <span v-else class="font-mono">{{ initials }}</span>
  </q-avatar>
</template>

<script setup>
import { computed } from 'vue'

const props = defineProps({
  user: { type: Object, default: () => ({}) },
  size: { type: String, default: '40px' }
})

const initials = computed(() => {
  const name = props.user?.name || props.user?.username || '?'
  return name.trim().substring(0, 1).toUpperCase()
})
</script>
