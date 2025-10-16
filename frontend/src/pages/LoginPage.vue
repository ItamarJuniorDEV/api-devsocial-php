<template>
  <q-form @submit.prevent="onSubmit" class="q-gutter-md">
    <q-input
      v-model="email"
      type="email"
      :label="$t('auth.email')"
      dark
      filled
      lazy-rules
      :rules="[(v) => !!v || 'Obrigatorio', (v) => /.+@.+\..+/.test(v) || 'Email invalido']"
    />
    <q-input
      v-model="password"
      type="password"
      :label="$t('auth.password')"
      dark
      filled
      lazy-rules
      :rules="[(v) => !!v || 'Obrigatorio']"
    />

    <q-btn
      type="submit"
      color="primary"
      class="full-width"
      :loading="auth.loading"
      :label="$t('auth.login')"
      unelevated
    />

    <div class="text-center text-muted q-mt-md">
      {{ $t('auth.noAccount') }}
      <router-link :to="{ name: 'Register' }" class="text-primary">{{ $t('auth.register') }}</router-link>
    </div>
  </q-form>
</template>

<script setup>
import { ref } from 'vue'
import { useRouter } from 'vue-router'
import { useAuthStore } from 'stores/auth'
import { notifyError } from 'src/utils/notify'

const router = useRouter()
const auth = useAuthStore()

const email = ref('')
const password = ref('')

async function onSubmit() {
  try {
    await auth.login({ email: email.value, password: password.value })
    router.push({ name: 'Feed' })
  } catch (e) {
    notifyError(e.response?.data?.message || 'Falha ao entrar')
  }
}
</script>
