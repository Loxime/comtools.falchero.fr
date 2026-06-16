export type AuthUser = {
  id: number
  email: string
  username: string | null
  roles: string[]
  isActive: boolean
  createdAt: string
}

type AuthResponse = {
  user: AuthUser
}

export const useAuthStore = defineStore('auth', {
  state: () => ({
    user: null as AuthUser | null
  }),

  getters: {
    isAdmin: (state) => state.user?.roles.includes('ROLE_ADMIN') === true
  },

  actions: {
    async login(email: string, password: string) {
      const config = useRuntimeConfig()
      const response = await $fetch<AuthResponse>(`${config.public.apiBase}/login`, {
        method: 'POST',
        credentials: 'include',
        body: { email, password }
      })

      this.user = response.user
      return response.user
    },

    async logout() {
      const config = useRuntimeConfig()
      await $fetch(`${config.public.apiBase}/logout`, {
        method: 'POST',
        credentials: 'include'
      })

      this.user = null
    },

    async fetchMe() {
      const config = useRuntimeConfig()

      try {
        const response = await $fetch<AuthResponse>(`${config.public.apiBase}/me`, {
          credentials: 'include'
        })
        this.user = response.user
        return response.user
      } catch {
        this.user = null
        return null
      }
    }
  }
})
