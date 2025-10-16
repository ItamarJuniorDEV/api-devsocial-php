<template>
  <q-card flat class="card-surface q-pa-md">
    <div class="row items-center q-gutter-sm">
      <UserAvatar :user="post.user" size="40px" class="cursor-pointer" @click="$emit('profile', post.user?.username)" />
      <div class="col">
        <div class="cursor-pointer" @click="$emit('profile', post.user?.username)">
          <b>{{ post.user?.name }}</b>
          <span class="text-muted q-ml-xs font-mono">@{{ post.user?.username }}</span>
        </div>
        <div class="text-muted text-caption">{{ formatRelative(post.created_at) }}</div>
      </div>
    </div>

    <div class="q-mt-sm" style="white-space: pre-wrap">{{ post.content }}</div>

    <div class="row q-mt-md q-gutter-md">
      <q-btn
        flat
        dense
        no-caps
        :icon="post.liked ? 'favorite' : 'favorite_border'"
        :color="post.liked ? 'accent' : ''"
        :label="post.likes_count || 0"
        @click="$emit('like')"
      />
      <q-btn
        flat
        dense
        no-caps
        icon="chat_bubble_outline"
        :label="post.comments_count || 0"
        @click="$emit('open')"
      />
      <q-btn
        v-if="!expanded"
        flat
        dense
        no-caps
        icon="open_in_new"
        @click="$emit('open')"
      />
    </div>
  </q-card>
</template>

<script setup>
import UserAvatar from 'components/UserAvatar.vue'
import { formatRelative } from 'src/utils/formatDate'

defineProps({
  post: { type: Object, required: true },
  expanded: { type: Boolean, default: false }
})

defineEmits(['like', 'open', 'profile'])
</script>
