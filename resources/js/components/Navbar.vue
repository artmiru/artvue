<template>
  <nav class="flex flex-wrap items-center justify-between">
    <a class="flex items-center text-foreground no-underline" href="/">
      <img class="mr-3" width="48" height="44" alt="Школа рисования для взрослых 'АртМир'" src="/assets/img/logo.webp" loading="lazy">
      <div class="text-left">
        <div class="text-xl font-bold leading-tight">ARTMIR.RU</div>
        <div class="text-sm text-violet-700">м. Звенигородская</div>
      </div>
    </a>
    <button class="md:hidden" @click="toggleMenu">
      <span v-if="!isMenuOpen">☰</span>
      <span v-else>✕</span>
    </button>
    <div class="w-full md:w-auto md:flex md:items-center" :class="{ 'hidden': !isMenuOpen }">
      <div class="flex flex-col md:flex-row md:ml-auto">
        <a href="#" class="py-2 md:py-0 md:px-4 border-b border-gray-200 md:border-b-0">Главная</a>
        <a href="#" class="py-2 md:py-0 md:px-4 border-b border-gray-200 md:border-b-0">О нас</a>
        <a href="#" class="py-2 md:py-0 md:px-4 border-b border-gray-200 md:border-b-0">Контакты</a>
         <Link
              v-if="props.auth?.user"
              :href="dashboard()"
              class="px-4 py-2 rounded-md bg-primary text-primary-foreground hover:bg-primary/90 transition-colors"
            >
              Dashboard
            </Link>

            <template v-else>
              <Link
                :href="login()"
                class="px-4 py-2 rounded-md bg-primary text-primary-foreground hover:bg-primary/90 transition-colors"
              >
                Log in
              </Link>

              <Link
                :href="register()"
                class="px-4 py-2 rounded-md bg-secondary text-secondary-foreground hover:bg-secondary/80 transition-colors"
              >
                Register
              </Link>
            </template>
      </div>
    </div>
  </nav>
</template>

<script setup lang="ts">
import { ref } from 'vue'
import { Link } from '@inertiajs/vue3'
import { dashboard, login, register } from '@/routes'

interface Props {
  auth?: {
    user?: any
  }
}

const props = withDefaults(defineProps<Props>(), {
  auth: () => ({ user: null })
})

const isMenuOpen = ref(false)

const toggleMenu = () => {
  isMenuOpen.value = !isMenuOpen.value
}
</script>

<style scoped>
/* All styles have been converted to Tailwind classes */
</style>
