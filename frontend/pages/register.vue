<template>
  <main>
    <h1>Créer un compte</h1>

    <form @submit.prevent="submit">
      <label>
        Email
        <input v-model="email" type="email" autocomplete="email" required>
      </label>

      <label>
        Mot de passe
        <input v-model="password" type="password" autocomplete="new-password" required>
      </label>

      <label>
        Username
        <input v-model="username" type="text" autocomplete="username">
      </label>

      <button type="submit">Créer mon compte</button>
      <p v-if="message">{{ message }}</p>
    </form>
  </main>
</template>

<script setup lang="ts">
const config = useRuntimeConfig()
const email = ref('')
const password = ref('')
const username = ref('')
const message = ref('')

async function submit() {
  message.value = ''

  try {
    await $fetch(`${config.public.apiBase}/register`, {
      method: 'POST',
      credentials: 'include',
      body: {
        email: email.value,
        password: password.value,
        username: username.value || null
      }
    })
    await navigateTo('/login')
  } catch {
    message.value = 'Impossible de créer le compte.'
  }
}
</script>
