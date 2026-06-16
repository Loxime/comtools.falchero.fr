import type { Config } from 'tailwindcss'

export default <Partial<Config>>{
  darkMode: 'class',
  theme: {
    extend: {
      fontFamily: {
        display: ['"Lilita One"', 'ui-rounded', 'system-ui', 'sans-serif']
      }
    }
  }
}
