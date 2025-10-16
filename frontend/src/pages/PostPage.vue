<template>
  <q-page class="q-pa-md bg-dark-page">
    <div class="post-wrap q-mx-auto">
      <PostCardSkeleton v-if="post.loading && !post.current" class="q-mb-md" />

      <PostCard
        v-else-if="post.current"
        :post="post.current"
        class="q-mb-md"
        expanded
        @like="post.toggleLike(post.current.id)"
        @profile="goProfile"
      />

      <EmptyState
        v-else
        icon="search_off"
        title="Post nao encontrado"
        hint="Pode ter sido removido."
      />

      <q-card v-if="post.current" flat class="card-surface q-pa-md q-mb-md">
        <CommentComposer @submit="onComment" :loading="commenting" />
      </q-card>

      <div v-if="post.comments.length">
        <q-card
          v-for="c in post.comments"
          :key="c.id"
          flat
          class="card-surface q-pa-md q-mb-sm"
        >
          <div class="row items-center q-gutter-sm">
            <UserAvatar :user="c.user" size="32px" />
            <div>
              <div>
                <b>{{ c.user?.name }}</b>
                <span class="text-muted q-ml-xs font-mono">@{{ c.user?.username }}</span>
              </div>
              <div class="text-muted text-caption">{{ formatRelative(c.created_at) }}</div>
            </div>
          </div>
          <div class="q-mt-sm" style="white-space: pre-wrap">{{ c.content }}</div>
        </q-card>
      </div>
      <EmptyState
        v-else-if="post.current && !post.loading"
        icon="comment"
        :title="$t('post.noComments')"
      />
    </div>
  </q-page>
</template>

<script setup>
import { ref, onMounted, watch } from 'vue'
import { useRoute, useRouter } from 'vue-router'
import { usePostStore } from 'stores/post'
import { formatRelative } from 'src/utils/formatDate'
import { notifyError } from 'src/utils/notify'
import PostCard from 'components/PostCard.vue'
import PostCardSkeleton from 'components/PostCardSkeleton.vue'
import CommentComposer from 'components/CommentComposer.vue'
import UserAvatar from 'components/UserAvatar.vue'
import EmptyState from 'components/EmptyState.vue'

const route = useRoute()
const router = useRouter()
const post = usePostStore()
const commenting = ref(false)

async function load() {
  try {
    await post.fetch(route.params.id)
  } catch {
    notifyError('Falha ao carregar post')
  }
}

onMounted(load)
watch(() => route.params.id, load)

async function onComment(content) {
  commenting.value = true
  try {
    await post.addComment(route.params.id, content)
  } catch {
    notifyError('Falha ao comentar')
  } finally {
    commenting.value = false
  }
}

function goProfile(username) {
  router.push({ name: 'Profile', params: { username } })
}
</script>

<style lang="scss" scoped>
.post-wrap {
  max-width: 640px;
}
</style>
