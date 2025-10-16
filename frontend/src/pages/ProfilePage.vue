<template>
  <q-page class="q-pa-md bg-dark-page">
    <div class="profile-wrap q-mx-auto">
      <q-card flat class="card-surface q-pa-md q-mb-md">
        <template v-if="loading && !profile">
          <q-skeleton type="QAvatar" size="96px" />
          <q-skeleton type="text" width="60%" class="q-mt-md" />
          <q-skeleton type="text" width="40%" />
        </template>
        <template v-else-if="profile">
          <div class="row items-center q-gutter-md">
            <UserAvatar :user="profile" size="96px" />
            <div class="col">
              <div class="text-h5">{{ profile.name }}</div>
              <div class="text-muted font-mono">@{{ profile.username }}</div>
              <div v-if="profile.bio" class="q-mt-sm">{{ profile.bio }}</div>
            </div>
            <div>
              <q-btn
                v-if="isMe"
                outline
                color="primary"
                :label="$t('profile.editProfile')"
                :to="{ name: 'ProfileEdit' }"
              />
              <q-btn
                v-else
                :outline="profile.is_following"
                color="primary"
                :label="profile.is_following ? $t('profile.unfollow') : $t('profile.follow')"
                :loading="followLoading"
                unelevated
                @click="toggleFollow"
              />
            </div>
          </div>
          <div class="row q-mt-md q-gutter-md text-muted">
            <div class="cursor-pointer" @click="tab = 'posts'">
              <b class="text-white">{{ profile.posts_count || 0 }}</b> {{ $t('profile.posts') }}
            </div>
            <div class="cursor-pointer" @click="tab = 'followers'">
              <b class="text-white">{{ profile.followers_count || 0 }}</b> {{ $t('profile.followers') }}
            </div>
            <div class="cursor-pointer" @click="tab = 'following'">
              <b class="text-white">{{ profile.following_count || 0 }}</b> {{ $t('profile.following') }}
            </div>
          </div>
        </template>
        <EmptyState v-else icon="person_off" :title="$t('profile.notFound')" />
      </q-card>

      <q-tabs v-model="tab" dense align="left" inline-label class="q-mb-md">
        <q-tab name="posts" :label="$t('profile.posts')" />
        <q-tab name="followers" :label="$t('profile.followers')" />
        <q-tab name="following" :label="$t('profile.following')" />
      </q-tabs>

      <q-tab-panels v-model="tab" animated class="bg-transparent">
        <q-tab-panel name="posts" class="q-pa-none">
          <PostCard
            v-for="post in posts"
            :key="post.id"
            :post="post"
            class="q-mb-md"
            @open="goPost(post.id)"
            @like="onLike(post.id)"
          />
          <EmptyState v-if="!posts.length" icon="article" title="Sem posts" />
        </q-tab-panel>
        <q-tab-panel name="followers" class="q-pa-none">
          <UserCard v-for="u in followers" :key="u.id" :user="u" class="q-mb-sm" />
          <EmptyState v-if="!followers.length" icon="group" title="Sem seguidores" />
        </q-tab-panel>
        <q-tab-panel name="following" class="q-pa-none">
          <UserCard v-for="u in following" :key="u.id" :user="u" class="q-mb-sm" />
          <EmptyState v-if="!following.length" icon="group" title="Nao segue ninguem" />
        </q-tab-panel>
      </q-tab-panels>
    </div>
  </q-page>
</template>

<script setup>
import { ref, computed, watch, onMounted } from 'vue'
import { useRoute, useRouter } from 'vue-router'
import { useAuthStore } from 'stores/auth'
import { useProfileStore } from 'stores/profile'
import { notifyError } from 'src/utils/notify'
import PostCard from 'components/PostCard.vue'
import UserCard from 'components/UserCard.vue'
import UserAvatar from 'components/UserAvatar.vue'
import EmptyState from 'components/EmptyState.vue'

const route = useRoute()
const router = useRouter()
const auth = useAuthStore()
const store = useProfileStore()

const tab = ref('posts')
const followLoading = ref(false)
const loading = ref(false)
const posts = ref([])
const followers = ref([])
const following = ref([])

const username = computed(() => route.params.username)
const profile = computed(() => store.getProfile(username.value))
const isMe = computed(() => auth.user?.username === username.value)

async function load() {
  loading.value = true
  try {
    await store.fetchProfile(username.value)
    posts.value = await store.fetchUserPosts(username.value)
  } catch {
    notifyError('Falha ao carregar perfil')
  } finally {
    loading.value = false
  }
}

watch(tab, async (v) => {
  try {
    if (v === 'followers' && !followers.value.length) {
      followers.value = await store.fetchFollowers(username.value)
    }
    if (v === 'following' && !following.value.length) {
      following.value = await store.fetchFollowing(username.value)
    }
  } catch {
    notifyError('Falha ao carregar lista')
  }
})

watch(() => route.params.username, () => {
  posts.value = []
  followers.value = []
  following.value = []
  tab.value = 'posts'
  load()
})

onMounted(load)

async function toggleFollow() {
  followLoading.value = true
  try {
    await store.toggleFollow(username.value)
  } catch {
    notifyError('Falha ao seguir')
  } finally {
    followLoading.value = false
  }
}

function onLike(id) {
  store.toggleLikeOnList(posts.value, id)
}

function goPost(id) {
  router.push({ name: 'Post', params: { id } })
}
</script>

<style lang="scss" scoped>
.profile-wrap {
  max-width: 720px;
}
</style>
