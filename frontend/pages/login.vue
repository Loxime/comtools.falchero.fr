<template>
  <main>
    <h1>Se connecter</h1>

    <form @submit.prevent="submit">
      <label>
        Email
        <input v-model="email" type="email" autocomplete="email" required>
      </label>

      <label>
        Mot de passe
        <input v-model="password" type="password" autocomplete="current-password" required>
      </label>

      <button type="submit">Se connecter</button>
      <p v-if="error">{{ error }}</p>
    </form>

    <NuxtLink to="/register">Créer un compte</NuxtLink>
  </main>
</template>

<script setup lang="ts">
const auth = useAuthStore()
const email = ref('')
const password = ref('')
const error = ref('')

async function submit() {
  error.value = ''

  try {
    await auth.login(email.value, password.value)
    await navigateTo('/')
  } catch {
    error.value = 'Identifiants invalides.'
  }
}
</script>
