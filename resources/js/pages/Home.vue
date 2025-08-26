<script setup lang="ts">
import { dashboard, login, register } from '@/routes';
import { Head, Link } from '@inertiajs/vue3';

// Определение типов для props
interface Props {
  auth?: {
    user?: object | null;
  };
  name?: string;
}

// Получение props с дефолтными значениями
const props = withDefaults(defineProps<Props>(), {
  auth: () => ({ user: null }),
  name: undefined
});

</script>

<template>
 <Head title="Welcome">
 <meta name="description" content="Welcome to {{ props.name }} - the best platform for managing your tasks and projects" />
</Head>
  <div class="min-h-screen bg-background">

    <header class="bg-card shadow">
      <div class="container mx-auto px-4 py-4">
        <nav class="flex justify-between items-center">
          <div class="text-xl font-bold">
            {{ props.name }}
          </div>

          <div class="flex space-x-4">
            <Link
              v-if="props.auth.user"
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
        </nav>
      </div>
    </header>

    <main class="container mx-auto px-4 py-8">
      <div class="max-w-3xl mx-auto text-center">
        <h1 class="text-4xl font-bold mb-4">Welcome to {{ props.name }}</h1>
        <p class="text-lg text-muted-foreground mb-8">
          The best platform for managing your tasks and projects
        </p>

        <div v-if="!props.auth.user" class="flex flex-col sm:flex-row justify-center gap-4">
          <Link
            :href="register()"
            class="px-6 py-3 rounded-md bg-primary text-primary-foreground hover:bg-primary/90 transition-colors text-lg font-medium"
          >
            Get Started
          </Link>

          <Link
            :href="login()"
            class="px-6 py-3 rounded-md bg-secondary text-secondary-foreground hover:bg-secondary/80 transition-colors text-lg font-medium"
          >
            Sign In
          </Link>
        </div>
      </div>
    </main>
  </div>
</template>
