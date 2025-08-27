<script setup lang="ts">
import { dashboard, login, register } from '@/routes';
import { Head, Link } from '@inertiajs/vue3';
import Courses from '@/components/Courses.vue';
import { ref, onMounted } from 'vue';

// Определение типов для курса, соответствующее компоненту Courses.vue
interface Course {
  id: number;
  name: string;
  description: string;
  image: string;
  alt: string;
  price: number;
  slug: string;
}

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

// Реактивная переменная для хранения курсов
const courses = ref<Course[]>([]);

// Функция для получения курсов с сервера
const fetchCourses = async () => {
  try {
    const response = await fetch('/api/courses');
    const data = await response.json();

    // Преобразуем данные в формат, ожидаемый компонентом Courses
    courses.value = data.map((course: any) => ({
      id: course.id,
      name: course.name,
      description: course.description,
      image: course.image,
      alt: course.alt,
      price: course.price,
      slug: course.slug
    }));
  } catch (error) {
    console.error('Ошибка при получении курсов:', error);
  }
};

// Получаем курсы при монтировании компонента
onMounted(() => {
  fetchCourses();
});

</script>

<template>
 <Head title="Welcome">
 <meta name="description" content="Welcome to {{ props.name }} - the best platform for managing your tasks and projects" />
</Head>
  <div class="min-h-screen bg-background">

    <header class="bg-card shadow">
      <div class="container mx-auto">
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

    <main class="container mx-auto">
       <div class="bg-neutral-100 py-8">
        <h1 class="text-center text-2xl sm:text-3xl md:text-5xl mb-8 font-medium leading-snug">Художественные курсы для взрослых<br> в Санкт-Петербурге</h1>
        <Courses :courses="courses" />
      </div>
    </main>
  </div>
</template>
