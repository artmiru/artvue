<script setup lang="ts">
import { dashboard, login, register } from '@/routes';
import { Head, Link } from '@inertiajs/vue3';
import TopNavigation from '@/components/TopNavigation.vue';
import Courses from '@/components/Courses.vue';
import Navbar from '@/components/Navbar.vue';
import { ref, onMounted } from 'vue';

// Определение типов для курса, соответствующее компоненту Courses.vue
interface Course {
  id: number;
  name: string;
  description: string;
  image: string;
  alt: string;
 formatted_price: number;
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
      formatted_price: course.formatted_price,
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
    <Navbar :auth="props.auth"/>
    <TopNavigation/>
 <Head title="Welcome">
 <meta name="description" content="Welcome to {{ props.name }} - the best platform for managing your tasks and projects" />
</Head>
  <div class="min-h-screen bg-background">

    <main class="container mx-auto">
       <div class="bg-neutral-100 py-8">
        <h1 class="text-center text-2xl sm:text-3xl md:text-5xl mb-8 font-medium leading-snug">Художественные курсы для взрослых<br> в Санкт-Петербурге</h1>
        <Courses :courses="courses" />
      </div>
    </main>
  </div>
</template>
