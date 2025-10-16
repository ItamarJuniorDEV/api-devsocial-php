<template>
  <q-card flat class="card-surface q-pa-md">
    <q-input
      v-model="content"
      type="textarea"
      autogrow
      dark
      borderless
      :placeholder="$t('feed.composerPlaceholder')"
      maxlength="500"
    />
    <div class="row items-center justify-between q-mt-sm">
      <div class="text-muted text-caption">{{ content.length }}/500</div>
      <q-btn
        color="primary"
        :disable="!content.trim() || loading"
        :loading="loading"
        :label="$t('feed.publish')"
        unelevated
        @click="publish"
      />
    </div>
  </q-card>
</template>

<script setup>
import { ref } from 'vue'
import { useFeedStore } from 'stores/feed'
import { notifyError } from 'src/utils/notify'

const emit = defineEmits(['published'])
const feed = useFeedStore()
const content = ref('')
const loading = ref(false)

async function publish() {
  loading.value = true
  try {
    await feed.createPost(content.value.trim())
    content.value = ''
    emit('published')
  } catch (e) {
    notifyError(e.response?.data?.message || 'Falha ao publicar')
  } finally {
    loading.value = false
  }
}
</script>
