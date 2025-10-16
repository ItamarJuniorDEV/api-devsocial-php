<template>
  <q-page class="q-pa-md bg-dark-page">
    <div class="search-wrap q-mx-auto">
      <q-input
        v-model="q"
        dark
        filled
        :placeholder="$t('search.placeholder')"
        class="q-mb-md sticky-search"
        @update:model-value="onTypeDebounced"
      >
        <template #prepend><q-icon name="search" /></template>
      </q-input>

      <q-tabs v-model="tab" dense align="left" inline-label class="q-mb-md" @update:model-value="syncUrl">
        <q-tab name="users" :label="$t('search.users')" />
        <q-tab name="posts" :label="$t('search.posts')" />
      </q-tabs>

      <EmptyState
        v-if="q.length < 2"
        icon="search"
        :title="$t('search.minChars')"
      />

      <template v-else>
        <div v-if="loading">
          <PostCardSkeleton v-for="i in 2" :key="i" class="q-mb-md" />
        </div>

        <template v-else>
          <template v-if="tab === 'users'">
            <UserCard
              v-for="u in search.users"
              :key="u.id"
              :user="u"
              class="q-mb-sm"
            />
            <EmptyState v-if="!search.users.length" icon="group_off" :title="$t('search.noResults')" />
          </template>

          <template v-else>
            <PostCard
              v-for="p in search.posts"
              :key="p.id"
              :post="p"
              class="q-mb-md"
              @open="goPost(p.id)"
            />
            <EmptyState v-if="!search.posts.length" icon="article" :title="$t('search.noResults')" />
          </template>
        </template>
      </template>
    </div>
  </q-page>
</template>

<script setup>
import { ref, watch, onMounted } from 'vue'
import { useRoute, useRouter } from 'vue-router'
import { useSearchStore } from 'stores/search'
import { notifyError } from 'src/utils/notify'
import PostCard from 'components/PostCard.vue'
import PostCardSkeleton from 'components/PostCardSkeleton.vue'
import UserCard from 'components/UserCard.vue'
import EmptyState from 'components/EmptyState.vue'

const route = useRoute()
const router = useRouter()
const search = useSearchStore()

const q = ref(route.query.q || '')
const tab = ref(route.query.tab || 'users')
const loading = ref(false)
let debounceTimer = null

function syncUrl() {
  router.replace({ query: { q: q.value, tab: tab.value } })
}

function onTypeDebounced() {
  clearTimeout(debounceTimer)
  debounceTimer = setTimeout(run, 300)
}

async function run() {
  syncUrl()
  if (q.value.length < 2) return
  loading.value = true
  try {
    if (tab.value === 'users') await search.searchUsers(q.value)
    else await search.searchPosts(q.value)
  } catch {
    notifyError('Falha na busca')
  } finally {
    loading.value = false
  }
}

watch(tab, run)

onMounted(() => {
  if (q.value.length >= 2) run()
})

function goPost(id) {
  router.push({ name: 'Post', params: { id } })
}
</script>

<style lang="scss" scoped>
.search-wrap {
  max-width: 640px;
}
.sticky-search {
  position: sticky;
  top: 64px;
  z-index: 10;
}
</style>
