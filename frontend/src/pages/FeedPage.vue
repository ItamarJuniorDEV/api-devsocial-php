<template>
  <q-page class="q-pa-md bg-dark-page">
    <div class="feed-wrap q-mx-auto">
      <PostComposer class="q-mb-md" @published="onPublished" />

      <div v-if="feed.loading && feed.posts.length === 0">
        <PostCardSkeleton v-for="i in 3" :key="i" class="q-mb-md" />
      </div>

      <EmptyState
        v-else-if="feed.posts.length === 0"
        icon="forum"
        :title="$t('feed.empty')"
        hint="Use o composer acima para publicar."
      />

      <q-infinite-scroll v-else :offset="250" @load="onLoad">
        <PostCard
          v-for="post in feed.posts"
          :key="post.id"
          :post="post"
          class="q-mb-md"
          @like="feed.toggleLike(post.id)"
          @open="goPost(post.id)"
          @profile="goProfile"
        />
        <template #loading>
          <div class="row justify-center q-my-md">
            <q-spinner color="primary" size="32px" />
          </div>
        </template>
      </q-infinite-scroll>
    </div>
  </q-page>
</template>

<script setup>
import { onMounted } from 'vue'
import { useRouter } from 'vue-router'
import { useFeedStore } from 'stores/feed'
import PostCard from 'components/PostCard.vue'
import PostComposer from 'components/PostComposer.vue'
import PostCardSkeleton from 'components/PostCardSkeleton.vue'
import EmptyState from 'components/EmptyState.vue'

const router = useRouter()
const feed = useFeedStore()

onMounted(() => {
  if (feed.posts.length === 0) feed.fetchMore()
})

async function onLoad(_index, done) {
  await feed.fetchMore()
  done(!feed.hasMore)
}

function onPublished() {
  feed.refresh()
}

function goPost(id) {
  router.push({ name: 'Post', params: { id } })
}

function goProfile(username) {
  router.push({ name: 'Profile', params: { username } })
}
</script>

<style lang="scss" scoped>
.feed-wrap {
  max-width: 640px;
}
</style>
