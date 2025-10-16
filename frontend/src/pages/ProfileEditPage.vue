<template>
  <q-page class="q-pa-md bg-dark-page">
    <div class="edit-wrap q-mx-auto">
      <q-card flat class="card-surface q-pa-lg">
        <div class="text-h6 q-mb-md">{{ $t('profile.editProfile') }}</div>

        <q-form @submit.prevent="onSave" class="q-gutter-md">
          <q-input v-model="form.name" :label="$t('auth.name')" dark filled />
          <q-input
            v-model="form.username"
            :label="$t('auth.username')"
            dark
            filled
            readonly
            hint="usuario nao pode ser alterado"
          />
          <q-input v-model="form.bio" :label="$t('profile.bio')" type="textarea" autogrow dark filled />

          <div class="row q-gutter-sm justify-end">
            <q-btn flat :label="$t('common.cancel')" @click="$router.back()" />
            <q-btn
              type="submit"
              color="primary"
              :loading="saving"
              :label="$t('common.save')"
              unelevated
            />
          </div>
        </q-form>
      </q-card>
    </div>
  </q-page>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import { useAuthStore } from 'stores/auth'
import { useProfileStore } from 'stores/profile'
import { notifyError, notifyOk } from 'src/utils/notify'

const auth = useAuthStore()
const store = useProfileStore()

const form = ref({ name: '', username: '', bio: '' })
const saving = ref(false)

onMounted(async () => {
  if (!auth.user) await auth.fetchUser()
  form.value.name = auth.user?.name || ''
  form.value.username = auth.user?.username || ''
  form.value.bio = auth.user?.bio || ''
})

async function onSave() {
  saving.value = true
  try {
    await store.updateMe({ name: form.value.name, bio: form.value.bio })
    await auth.fetchUser()
    notifyOk('Perfil atualizado')
  } catch {
    notifyError('Falha ao salvar')
  } finally {
    saving.value = false
  }
}
</script>

<style lang="scss" scoped>
.edit-wrap {
  max-width: 560px;
}
</style>
