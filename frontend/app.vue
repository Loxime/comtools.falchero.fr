<template>
  <main class="page">
    <section class="panel">
      <p class="eyebrow">ComTools</p>
      <h1>Gestion de communication</h1>
      <p class="copy">
        Interface Nuxt 3 prete a consommer l'API Symfony.
      </p>
      <button type="button" @click="checkApi">
        Tester l'API
      </button>
      <pre v-if="apiStatus">{{ apiStatus }}</pre>
    </section>
  </main>
</template>

<script setup lang="ts">
const config = useRuntimeConfig()
const apiStatus = ref('')

async function checkApi() {
  const response = await $fetch(`${config.public.apiBase}/status`)
  apiStatus.value = JSON.stringify(response, null, 2)
}
</script>

<style>
body {
  margin: 0;
  font-family: Inter, ui-sans-serif, system-ui, -apple-system, BlinkMacSystemFont, "Segoe UI", sans-serif;
  color: #172026;
  background: #f4f6f8;
}

.page {
  min-height: 100vh;
  display: grid;
  place-items: center;
  padding: 32px;
}

.panel {
  width: min(100%, 620px);
  padding: 32px;
  border: 1px solid #d8dee4;
  border-radius: 8px;
  background: #ffffff;
}

.eyebrow {
  margin: 0 0 8px;
  font-size: 14px;
  font-weight: 700;
  color: #1967d2;
}

h1 {
  margin: 0;
  font-size: 36px;
  line-height: 1.1;
}

.copy {
  margin: 16px 0 24px;
  color: #53616d;
}

button {
  min-height: 40px;
  padding: 0 16px;
  border: 0;
  border-radius: 6px;
  color: #ffffff;
  background: #1967d2;
  font-weight: 700;
  cursor: pointer;
}

pre {
  margin: 24px 0 0;
  padding: 16px;
  overflow: auto;
  border-radius: 6px;
  background: #172026;
  color: #f4f6f8;
}
</style>
