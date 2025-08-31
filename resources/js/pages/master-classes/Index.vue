<script setup lang="ts">
import { ref, onMounted } from 'vue';
import { Head, Link } from '@inertiajs/vue3';
import Navbar from '@/components/Navbar.vue';
import { Button } from '@/components/ui/button';
import Breadcrumbs from '@/components/Breadcrumbs.vue';
import MasterClassImage from '@/components/MasterClassImage.vue';

// Интерфейс для мастер-класса
interface MasterClass {
  id: number;
  title: string;
  image_path: string;
  page_title: string | null;
  meta_description: string | null;
  price: number;
  formatted_price: string;
  slug: string;
  max_participants: number;
  booked_places: number;
  tags: string[] | null;
  next_event_date: string;
  formatted_next_event_date: string;
  created_at: string;
  updated_at: string;
}

// Данные мастер-классов
const masterClasses = ref<MasterClass[]>([]);
const loading = ref(true);
const error = ref<string | null>(null);

// Загрузка мастер-классов с API
const fetchMasterClasses = async () => {
  try {
    loading.value = true;
    error.value = null;

    const response = await fetch(`/api/master-classes`);
    masterClasses.value = await response.json();
  } catch (err) {
    console.error('Ошибка при загрузке мастер-классов:', err);
    error.value = 'Ошибка при загрузке мастер-классов';
  } finally {
    loading.value = false;
  }
};

// Загрузка мастер-классов при монтировании компонента
onMounted(() => {
  fetchMasterClasses();
});

// Хлебные крошки
const breadcrumbs = [
  { title: 'Главная', href: '/' },
  { title: 'Мастер-классы' }
];

</script>

<template>
  <div class="min-h-screen bg-background">
    <Navbar />
    <Breadcrumbs :breadcrumbs="breadcrumbs" class="mb-6 lg:w-fit lg:m-auto py-0.5" />

    <!-- Состояние загрузки -->
    <div v-if="loading" class="container mx-auto px-4 py-8 text-center">
      <p class="text-xl text-neutral-700">Загрузка мастер-классов...</p>
    </div>

    <!-- Ошибка -->
    <div v-else-if="error" class="container mx-auto px-4 py-8 text-center">
      <p class="text-xl text-red-500">{{ error }}</p>
      <Button @click="fetchMasterClasses" class="mt-4">Повторить попытку</Button>
    </div>

    <!-- Список мастер-классов -->
    <div v-else class="container lg:w-10/12 mx-auto">
      <Head title="Мастер-классы" />

      <!-- Заголовок -->
      <div class="text-center mb-12">
        <h1 class="text-4xl md:text-5xl font-bold text-neutral-900 mb-4">
          Наши мастер-классы
        </h1>
        <p class="text-xl text-neutral-700 max-w-3xl mx-auto">
          Присоединяйтесь к нашим мастер-классам и получите новые навыки под руководством опытных преподавателей
        </p>
      </div>

      <!-- Список мастер-классов -->
      <div v-if="masterClasses.length > 0" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
        <div
          v-for="masterClass in masterClasses"
          :key="masterClass.id"
          class="bg-white rounded-lg shadow-md overflow-hidden hover:shadow-lg transition-shadow duration-300 flex flex-col"
          style="height: 450px;"
        >
          <Link :href="`/mk/${masterClass.slug}`" class="flex flex-col h-full">
            <!-- Изображение мастер-класса -->
            <MasterClassImage
              :image-path="masterClass.image_path"
              :alt="masterClass.title"
            />

            <!-- Информация о мастер-классе -->
            <div class="p-6 flex flex-col flex-grow">
              <h2 class="text-xl font-semibold text-neutral-900 mb-2">{{ masterClass.title }}</h2>

              <!-- Дата ближайшего события -->
              <div class="mb-2">
                <span class="text-sm text-neutral-600">
                  {{ masterClass.formatted_next_event_date }}
                </span>
              </div>

              <div class="flex justify-between items-center mb-4">
                <span class="text-2xl font-bold text-red-500">{{ masterClass.formatted_price }}</span>
                <span class="text-neutral-500">за участие</span>
              </div>

              <!-- Информация о местах -->
              <div class="mb-4 flex-grow">
                <div class="flex justify-between mb-1">
                  <span class="text-neutral-700 text-sm">Свободных мест:</span>
                  <span class="text-sm font-semibold">{{ masterClass.max_participants - masterClass.booked_places }} из {{ masterClass.max_participants }}</span>
                </div>
                <div class="w-full bg-gray-200 rounded-full h-2">
                  <div
                    class="bg-red-500 h-2 rounded-full"
                    :style="{ width: `${((masterClass.max_participants - masterClass.booked_places) / masterClass.max_participants) * 100}%` }"
                  ></div>
                </div>
              </div>

              <div class="mt-auto">
                <Button class="w-full bg-red-500 hover:bg-red-600 text-white">
                  Подробнее
                </Button>
              </div>
            </div>
          </Link>
        </div>
      </div>

      <!-- Пустой список -->
      <div v-else class="text-center py-12">
        <p class="text-xl text-neutral-700">Мастер-классы пока не доступны</p>
        <p class="text-neutral-500 mt-2">Пожалуйста, загляните позже</p>
      </div>
    </div>
  </div>
</template>

<style scoped>
/* Дополнительные стили при необходимости */
</style>
