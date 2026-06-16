export default defineNuxtRouteMiddleware(async () => {
  const auth = useAuthStore()

  if (auth.user === null) {
    await auth.fetchMe()
  }

  if (auth.user === null) {
    return navigateTo('/login')
  }
})
