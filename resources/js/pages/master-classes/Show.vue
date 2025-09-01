<script setup lang="ts">
import { ref, onMounted, computed } from 'vue';
import { Head, Link } from '@inertiajs/vue3';
import Navbar from '@/components/navigation/Navbar.vue';
import { Button } from '@/components/ui/button';
import Breadcrumbs from '@/components/navigation/Breadcrumbs.vue';
import LoadingSpinner from '@/components/common/LoadingSpinner.vue';

// Интерфейс для мастер-класса
interface MasterClass {
  id: number;
  title: string;
  image_path: string;
  page_title: string | null;
  meta_description: string | null;
  price: number;
  slug: string;
  max_participants: number;
  booked_places: number;
  tags: string[] | null;
 created_at: string;
  updated_at: string;
}

// Данные мастер-класса
const masterClass = ref<MasterClass | null>(null);
const loading = ref(true);
const error = ref<string | null>(null);

// Получение slug из URL
const props = defineProps({
  slug: {
    type: String,
    required: true
  }
});

// Загрузка мастер-класса с API
const fetchMasterClass = async () => {
  try {
    loading.value = true;
    error.value = null;

    const response = await fetch(`/api/master-classes`);
    const data: MasterClass[] = await response.json();

    // Найти мастер-класс по slug
    const foundMasterClass = data.find(mc => mc.slug === props.slug);
    if (foundMasterClass) {
      masterClass.value = foundMasterClass;
    } else {
      error.value = 'Мастер-класс не найден';
    }
  } catch (err) {
    console.error('Ошибка при загрузке мастер-класса:', err);
    error.value = 'Ошибка при загрузке мастер-класса';
  } finally {
    loading.value = false;
  }
};

// Загрузка мастер-класса при монтировании компонента
onMounted(() => {
  fetchMasterClass();
});

// Хлебные крошки
const breadcrumbs = computed(() => [
  { title: 'Главная', href: '/' },
  { title: 'Мастер-классы', href: '/mk' },
  { title: masterClass.value?.title || 'Мастер-класс' }
]);

// Вычисляем количество свободных мест
const availablePlaces = computed(() => {
  if (masterClass.value) {
    return masterClass.value.max_participants - masterClass.value.booked_places;
  }
  return 0;
});
</script>

<template>
 <div class="min-h-screen bg-background">
    <Navbar />
    <Breadcrumbs :breadcrumbs="breadcrumbs" class="mb-6 lg:w-fit lg:m-auto py-0.5" />

    <!-- Состояние загрузки -->
    <div v-if="loading" class="container mx-auto px-4 py-8 text-center">
      <LoadingSpinner text="Загрузка мастер-класса..." color="red" size="md" />
    </div>

    <!-- Ошибка -->
    <div v-else-if="error" class="container mx-auto px-4 py-8 text-center">
      <p class="text-xl text-red-500">{{ error }}</p>
      <Link href="/" class="text-blue-500 hover:underline mt-4 block">
        Вернуться на главную
      </Link>
    </div>

    <!-- Мастер-класс найден -->
    <div v-else-if="masterClass" class="container mx-auto px-4 py-8">
      <Head :title="masterClass.page_title || masterClass.title" />

      <!-- Заголовок -->
      <div class="text-center mb-12">
        <h1 class="text-4xl md:text-5xl font-bold text-neutral-900 mb-4">
          {{ masterClass.title }}
        </h1>
        <p class="text-xl text-neutral-700 max-w-3xl mx-auto">
          Присоединяйтесь к нашему мастер-классу и получите новые навыки
        </p>
      </div>

      <!-- Основной контент мастер-класса -->
      <div class="grid grid-cols-1 lg:grid-cols-2 gap-12 mb-16">
        <!-- Изображение мастер-класса -->
        <div>
          <img
            :src="`/assets/img/mk/oil/thumbs/${masterClass.image_path}.webp`"
            :alt="masterClass.title"
            class="w-full rounded-lg shadow-md"
          />
        </div>

        <!-- Информация о мастер-классе -->
        <div>
          <h2 class="text-3xl font-semibold text-neutral-900 mb-4">{{ masterClass.title }}</h2>

          <div class="bg-neutral-50 p-6 rounded-lg mb-6">
            <div class="flex justify-between items-center mb-4">
              <span class="text-3xl font-bold text-red-500">{{ (masterClass.price / 100).toFixed(2) }} ₽</span>
              <span class="text-neutral-500">за участие</span>
            </div>

            <!-- Информация о местах -->
            <div class="mb-4">
              <div class="flex justify-between mb-1">
                <span class="text-neutral-700">Свободных мест:</span>
                <span class="font-semibold">{{ availablePlaces }} из {{ masterClass.max_participants }}</span>
              </div>
              <div class="w-full bg-gray-200 rounded-full h-2.5">
                <div
                  class="bg-red-500 h-2.5 rounded-full"
                  :style="{ width: `${(availablePlaces / masterClass.max_participants) * 100}%` }"
                ></div>
              </div>
            </div>

            <Button
              class="w-full bg-red-500 hover:bg-red-600 text-white text-lg py-3"
              :disabled="availablePlaces <= 0"
            >
              {{ availablePlaces > 0 ? 'Записаться на мастер-класс' : 'Мест нет' }}
            </Button>
          </div>

          <!-- Теги -->
          <div v-if="masterClass.tags && masterClass.tags.length > 0" class="mb-6">
            <h3 class="text-lg font-semibold text-neutral-900 mb-2">Теги:</h3>
            <div class="flex flex-wrap gap-2">
              <span
                v-for="tag in masterClass.tags"
                :key="tag"
                class="px-3 py-1 bg-blue-100 text-blue-800 rounded-full text-sm"
              >
                {{ tag }}
              </span>
            </div>
          </div>
        </div>
      </div>

      <!-- Дополнительная информация -->
      <div class="bg-white rounded-lg shadow-md p-8 mb-12">
        <h3 class="text-2xl font-semibold text-neutral-900 mb-4">О мастер-классе</h3>
        <p class="text-neutral-700">
          Этот мастер-класс предлагает уникальную возможность научиться новым техникам под руководством опытных преподавателей.
          Мы обеспечиваем индивидуальный подход к каждому участнику и создаем комфортную атмосферу для творчества.
        </p>
      </div>
    </div>

    <!-- Мастер-класс не найден (запасной вариант) -->
    <div v-else class="container mx-auto px-4 py-8 text-center">
      <p class="text-xl text-neutral-700">Мастер-класс не найден</p>
      <Link href="/" class="text-blue-500 hover:underline mt-4 block">
        Вернуться на главную
      </Link>
    </div>
  </div>
</template>

<style scoped>
/* Дополнительные стили при необходимости */
</style>
