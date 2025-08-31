<script setup lang="ts">
import { ref, onMounted, computed } from 'vue';
import { Head, Link } from '@inertiajs/vue3';
import Navbar from '@/components/Navbar.vue';
import { Button } from '@/components/ui/button';
import Breadcrumbs from '@/components/Breadcrumbs.vue';
import LoadingSpinner from '@/components/LoadingSpinner.vue';

// Интерфейс для курса
interface Course {
  id: number;
  name: string;
  image: string;
  alt: string;
  description: string;
  price: number;
  slug: string;
  meta_title: string | null;
  page_header: string | null;
  page_subheader: string | null;
  meta_description: string | null;
  breadcrumbs: string | null;
  created_at: string;
  updated_at: string;
}

// Данные курса
const course = ref<Course | null>(null);
const loading = ref(true);
const error = ref<string | null>(null);

// Получение slug из URL
const props = defineProps({
  slug: {
    type: String,
    required: true
  }
});

// Загрузка курса с API
const fetchCourse = async () => {
  try {
    loading.value = true;
    error.value = null;

    const response = await fetch(`/api/courses`);
    const data: Course[] = await response.json();

    // Найти курс по slug
    const foundCourse = data.find(c => c.slug === props.slug);
    if (foundCourse) {
      course.value = foundCourse;
    } else {
      error.value = 'Курс не найден';
    }
  } catch (err) {
    console.error('Ошибка при загрузке курса:', err);
    error.value = 'Ошибка при загрузке курса';
  } finally {
    loading.value = false;
  }
};

// Загрузка курса при монтировании компонента
onMounted(() => {
  fetchCourse();
});

// Хлебные крошки
const breadcrumbs = computed(() => [
  { title: 'Главная', href: '/' },
  { title: course.value?.breadcrumbs || 'Курс' }
]);
</script>

<template>

  <div class="min-h-screen bg-background">
    <Navbar/>
    <Breadcrumbs :breadcrumbs="breadcrumbs" class="mb-6 lg:w-fit lg:m-auto py-0.5" />
    <!-- Состояние загрузки -->
    <div v-if="loading" class="container mx-auto px-4 py-8 text-center">
      <LoadingSpinner text="Загрузка курса..." color="red" size="md" />
    </div>

    <!-- Ошибка -->
    <div v-else-if="error" class="container mx-auto px-4 py-8 text-center">
      <p class="text-xl text-red-500">{{ error }}</p>
      <Link href="/" class="text-blue-500 hover:underline mt-4 block">
        Вернуться на главную
      </Link>
    </div>

    <!-- Курс найден -->
    <div v-else-if="course" class="container mx-auto px-4 py-8">
      <Head :title="course.meta_title || course.name" />



      <!-- Заголовок и подзаголовок -->
      <div class="text-center mb-12 m-auto">
        <h1 class="text-4xl md:text-5xl font-bold text-neutral-900 mb-5">
          {{ course.page_header || course.name }}
        </h1>
        <p class="text-2xl text-neutral-700 max-w-3xl mx-auto w-10/12">
          {{ course.page_subheader || course.description }}
        </p>
      </div>

      <!-- Основной контент курса -->
      <div class="grid grid-cols-1 lg:grid-cols-2 gap-12 mb-16">
        <!-- Изображение курса -->
        <div>
          <img
            :src="`/assets/img/main/${course.image}`"
            :alt="course.alt"
            class="w-full rounded-lg shadow-md"
          />
        </div>

        <!-- Информация о курсе -->
        <div>
          <h2 class="text-3xl font-semibold text-neutral-900 mb-4">{{ course.name }}</h2>
          <p class="text-neutral-700 mb-6 text-lg">{{ course.description }}</p>

          <div class="bg-neutral-50 p-6 rounded-lg mb-6">
            <div class="flex justify-between items-center mb-4">
              <span class="text-3xl font-bold text-red-500">{{ (course.price / 100).toFixed(2) }} ₽</span>
              <span class="text-neutral-500">за курс</span>
            </div>
            <Button class="w-full bg-red-500 hover:bg-red-600 text-white text-lg py-3">
              Записаться на курс
            </Button>
          </div>
        </div>
      </div>

      <!-- Дополнительная информация -->
      <div class="bg-white rounded-lg shadow-md p-8 mb-12">
        <h3 class="text-2xl font-semibold text-neutral-900 mb-4">О курсе</h3>
        <p class="text-neutral-700">{{ course.description }}</p>
      </div>
    </div>

    <!-- Курс не найден (запасной вариант) -->
    <div v-else class="container mx-auto px-4 py-8 text-center">
      <p class="text-xl text-neutral-700">Курс не найден</p>
      <Link href="/" class="text-blue-500 hover:underline mt-4 block">
        Вернуться на главную
      </Link>
    </div>
  </div>
</template>

<style scoped>
/* Дополнительные стили при необходимости */
</style>
