<template>
  <main>
    <h1>Admin</h1>

    <table>
      <thead>
        <tr>
          <th>Email</th>
          <th>Username</th>
          <th>Rôles</th>
          <th>Statut</th>
          <th>Actions</th>
        </tr>
      </thead>
      <tbody>
        <tr v-for="user in users" :key="user.id">
          <td>{{ user.email }}</td>
          <td>{{ user.username || '' }}</td>
          <td>
            <select :value="roleValue(user)" @change="changeRole(user, $event)">
              <option value="user">Utilisateur</option>
              <option value="admin">Admin</option>
            </select>
          </td>
          <td>{{ user.isActive ? 'Actif' : 'Désactivé' }}</td>
          <td>
            <button type="button" @click="toggleActive(user)">
              {{ user.isActive ? 'Désactiver' : 'Réactiver' }}
            </button>
          </td>
        </tr>
      </tbody>
    </table>

    <p v-if="error">{{ error }}</p>
  </main>
</template>

<script setup lang="ts">
import type { AuthUser } from '~/stores/auth'

definePageMeta({
  middleware: 'admin'
})

const config = useRuntimeConfig()
const users = ref<AuthUser[]>([])
const error = ref('')
let polling: ReturnType<typeof setInterval> | undefined

async function fetchUsers() {
  error.value = ''

  try {
    const response = await $fetch<{ users: AuthUser[] }>(`${config.public.apiBase}/admin/users`, {
      credentials: 'include'
    })
    users.value = response.users
  } catch {
    error.value = 'Accès admin refusé.'
    await navigateTo('/login')
  }
}

async function updateUser(user: AuthUser, payload: Partial<Pick<AuthUser, 'roles' | 'isActive'>>) {
  const response = await $fetch<{ user: AuthUser }>(`${config.public.apiBase}/admin/users/${user.id}`, {
    method: 'PATCH',
    credentials: 'include',
    body: payload
  })

  const index = users.value.findIndex((item) => item.id === user.id)
  if (index !== -1) {
    users.value[index] = response.user
  }
}

function roleValue(user: AuthUser) {
  return user.roles.includes('ROLE_ADMIN') ? 'admin' : 'user'
}

async function changeRole(user: AuthUser, event: Event) {
  const value = (event.target as HTMLSelectElement).value
  await updateUser(user, {
    roles: value === 'admin' ? ['ROLE_USER', 'ROLE_ADMIN'] : ['ROLE_USER']
  })
}

async function toggleActive(user: AuthUser) {
  await updateUser(user, {
    isActive: !user.isActive
  })
}

onMounted(async () => {
  await fetchUsers()
  polling = setInterval(fetchUsers, 30000)
})

onBeforeUnmount(() => {
  if (polling !== undefined) {
    clearInterval(polling)
  }
})
</script>
