<template>
  <header class="sticky top-0 z-50 border-b border-slate-200 bg-white/95 backdrop-blur dark:border-slate-800 dark:bg-slate-950/95">
    <div class="grid h-16 grid-cols-[auto_1fr_auto] items-center gap-3 px-4 sm:px-6">
      <NuxtLink to="/" class="flex items-center gap-2" aria-label="ComTools">
        <span class="grid size-10 place-items-center rounded-full bg-gradient-to-br from-violet-600 to-blue-500 text-lg font-black text-white shadow-sm">
          C
        </span>
      </NuxtLink>

      <p class="justify-self-center bg-gradient-to-r from-violet-600 to-blue-500 bg-clip-text px-2 font-display text-3xl text-transparent drop-shadow-[0_2px_0_rgba(30,41,59,0.14)] sm:text-4xl">
        ComTools
      </p>

      <div ref="accountRoot" class="flex items-center gap-1 sm:gap-2">
        <button
          type="button"
          class="grid size-10 place-items-center rounded-full text-slate-700 hover:bg-slate-100 dark:text-slate-100 dark:hover:bg-slate-800"
          :aria-label="t('theme')"
          @click="toggleTheme"
        >
          <FontAwesomeIcon :icon="isDark ? 'moon' : 'sun'" />
        </button>

        <button
          type="button"
          class="grid size-10 place-items-center rounded-full text-xl hover:bg-slate-100 dark:hover:bg-slate-800"
          :aria-label="t('language')"
          @click="toggleLocale"
        >
          {{ locale === 'fr' ? '🇫🇷' : '🇬🇧' }}
        </button>

        <button
          type="button"
          class="grid size-10 place-items-center overflow-hidden rounded-full text-slate-700 hover:bg-slate-100 dark:text-slate-100 dark:hover:bg-slate-800"
          :aria-label="t('account')"
          @click="isDropdownOpen = !isDropdownOpen"
        >
          <span v-if="auth.user" class="grid size-9 place-items-center rounded-full bg-blue-600 text-sm font-bold text-white">
            {{ initials }}
          </span>
          <FontAwesomeIcon v-else icon="circle-user" class="text-2xl" />
        </button>

        <div
          v-if="isDropdownOpen"
          class="absolute right-4 top-14 w-56 rounded-md border border-slate-200 bg-white py-2 shadow-lg dark:border-slate-800 dark:bg-slate-900"
        >
          <NuxtLink
            v-for="item in menuItems"
            :key="item.label"
            :to="item.to"
            class="block px-4 py-2 text-sm text-slate-700 hover:bg-slate-100 dark:text-slate-100 dark:hover:bg-slate-800"
            @click="isDropdownOpen = false"
          >
            {{ item.label }}
          </NuxtLink>

          <button
            v-if="auth.user"
            type="button"
            class="block w-full px-4 py-2 text-left text-sm text-slate-700 hover:bg-slate-100 dark:text-slate-100 dark:hover:bg-slate-800"
            @click="logout"
          >
            {{ t('logout') }}
          </button>
        </div>
      </div>
    </div>
  </header>
</template>

<script setup lang="ts">
const auth = useAuthStore()
const { t, locale, setLocale } = useI18n()
const isDark = ref(false)
const isDropdownOpen = ref(false)
const accountRoot = ref<HTMLElement | null>(null)

const initials = computed(() => {
  const source = auth.user?.username || auth.user?.email || ''
  return source.slice(0, 2).toUpperCase()
})

const menuItems = computed(() => {
  if (!auth.user) {
    return [
      { label: t('createAccount'), to: '/register' },
      { label: t('settings'), to: '/settings' }
    ]
  }

  const items = [
    { label: t('profile'), to: '/user/profile' },
    { label: t('settings'), to: '/settings' },
    { label: t('tickets'), to: '/user/tickets' }
  ]

  if (auth.user.roles.includes('ROLE_ADMIN')) {
    items.unshift({ label: t('administration'), to: '/admin' })
  }

  return items
})

function applyTheme(value: boolean) {
  isDark.value = value
  document.documentElement.classList.toggle('dark', value)
  localStorage.setItem('theme', value ? 'dark' : 'light')
}

function toggleTheme() {
  applyTheme(!isDark.value)
}

async function toggleLocale() {
  await setLocale(locale.value === 'fr' ? 'en' : 'fr')
}

async function logout() {
  isDropdownOpen.value = false
  await auth.logout()
  await navigateTo('/login')
}

function onClickOutside(event: MouseEvent) {
  const target = event.target

  if (target instanceof Node && !accountRoot.value?.contains(target)) {
    isDropdownOpen.value = false
  }
}

onMounted(async () => {
  const savedTheme = localStorage.getItem('theme')
  applyTheme(savedTheme === 'dark')
  await auth.fetchMe()
  window.addEventListener('click', onClickOutside)
})

onBeforeUnmount(() => {
  window.removeEventListener('click', onClickOutside)
})
</script>
