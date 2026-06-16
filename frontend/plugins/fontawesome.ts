import { FontAwesomeIcon } from '@fortawesome/vue-fontawesome'
import { library, config } from '@fortawesome/fontawesome-svg-core'
import { faCircleUser, faMoon, faSun } from '@fortawesome/free-solid-svg-icons'
import '@fortawesome/fontawesome-svg-core/styles.css'

export default defineNuxtPlugin((nuxtApp) => {
  config.autoAddCss = false
  library.add(faCircleUser, faMoon, faSun)
  nuxtApp.vueApp.component('FontAwesomeIcon', FontAwesomeIcon)
})
