<template>
  <q-page class="row items-center justify-center bg-dark-page">
    <div class="text-center q-pa-lg">
      <q-icon name="error_outline" size="64px" color="primary" />
      <div class="text-h5 q-mt-md">{{ $t('notFound.title') }}</div>
      <q-btn
        color="primary"
        class="q-mt-md"
        unelevated
        :label="$t('notFound.back')"
        :to="{ name: 'Feed' }"
      />
    </div>
  </q-page>
</template>

<script setup>
//
</script>
