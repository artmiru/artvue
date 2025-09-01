<script setup lang="ts">
import { Head } from '@inertiajs/vue3';
import Navbar from '@/components/Navbar.vue';
import Courses from '@/components/Courses.vue';
import TeachersSection from '@/components/TeachersSection.vue';
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

// Определение типов для учителя
interface Teacher {
  id: number;
  user_id: number;
  about: string;
  phone: string;
  folder: string;
 alt: string | null;
  keypass_code: string;
  created_at: string;
  updated_at: string;
  deleted_at: string | null;
  user: {
    id: number;
    first_name: string;
    last_name: string | null;
    middle_name: string | null;
    phone: string;
    email: string | null;
    role: string;
    created_at: string;
    updated_at: string;
  };
}

// Определение типов для props
interface Props {
  auth?: {
    user?: object | null;
  };
  name?: string;
  teachers?: Teacher[];
}

// Получение props с дефолтными значениями
const props = withDefaults(defineProps<Props>(), {
  auth: () => ({ user: null }),
  name: undefined,
  teachers: () => []
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
    <!-- <Navbar :auth="props.auth"/> -->
    <Navbar :auth="props.auth"/>
 <Head title="Welcome">
 <meta name="description" content="Welcome to {{ props.name }} - the best platform for managing your tasks and projects" />
</Head>
  <div class="min-h-screen bg-background">

    <main class="container mx-auto">
       <div class="bg-neutral-50 py-8">
        <h1 class="text-center text-3xl md:text-5xl mb-8 font-medium leading-snug">Художественные курсы для взрослых<br> в Санкт-Петербурге</h1>
        <Courses :courses="courses" />
      </div>
      <TeachersSection :teachers="props.teachers" />
    </main>
  </div>
</template>
