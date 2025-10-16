<template>
  <div class="text-center q-pa-xl text-muted">
    <q-icon :name="icon" size="64px" />
    <div class="text-h6 q-mt-sm text-white">{{ title }}</div>
    <div v-if="hint" class="q-mt-xs">{{ hint }}</div>
  </div>
</template>

<script setup>
defineProps({
  icon: { type: String, default: 'inbox' },
  title: { type: String, required: true },
  hint: { type: String, default: '' }
})
</script>
