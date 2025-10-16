<template>
  <q-card flat class="card-surface q-pa-md row items-center q-gutter-md">
    <UserAvatar :user="user" size="48px" class="cursor-pointer" @click="goProfile" />
    <div class="col cursor-pointer" @click="goProfile">
      <div><b>{{ user.name }}</b></div>
      <div class="text-muted font-mono">@{{ user.username }}</div>
      <div v-if="user.bio" class="text-muted text-caption">{{ user.bio }}</div>
    </div>
    <q-btn
      v-if="showFollow && !isSelf"
      :outline="isFollowing"
      color="primary"
      :label="isFollowing ? $t('profile.unfollow') : $t('profile.follow')"
      :loading="loading"
      unelevated
      @click.stop="toggle"
    />
  </q-card>
</template>

<script setup>
import { ref, computed, watch } from 'vue'
import { useRouter } from 'vue-router'
import { useAuthStore } from 'stores/auth'
import { useProfileStore } from 'stores/profile'
import { notifyError } from 'src/utils/notify'
import UserAvatar from 'components/UserAvatar.vue'

const props = defineProps({
  user: { type: Object, required: true },
  showFollow: { type: Boolean, default: true }
})
const emit = defineEmits(['toggle'])

const router = useRouter()
const auth = useAuthStore()
const store = useProfileStore()
const loading = ref(false)

const localFollowing = ref(!!props.user.is_following)
watch(
  () => props.user.is_following,
  (v) => {
    localFollowing.value = !!v
  }
)

const isFollowing = computed(() => localFollowing.value)
const isSelf = computed(() => auth.user?.username === props.user.username)

function goProfile() {
  router.push({ name: 'Profile', params: { username: props.user.username } })
}

async function toggle() {
  if (isSelf.value) return
  loading.value = true
  const prev = localFollowing.value
  localFollowing.value = !prev
  try {
    await store.toggleFollow(props.user.username)
    emit('toggle', { username: props.user.username, following: !prev })
  } catch {
    localFollowing.value = prev
    notifyError('Falha ao seguir')
  } finally {
    loading.value = false
  }
}
</script>
