export default defineNuxtRouteMiddleware(async () => {
  const auth = useAuthStore()

  await auth.fetchMe()

  if (auth.user === null || !auth.user.roles.includes('ROLE_ADMIN')) {
    return navigateTo('/login')
  }
})
