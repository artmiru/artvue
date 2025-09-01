<script setup lang="ts">
import { ref, onMounted } from 'vue';
import { Head, Link } from '@inertiajs/vue3';
import Navbar from '@/components/navigation/Navbar.vue';
import { Button } from '@/components/ui/button';
import Breadcrumbs from '@/components/navigation/Breadcrumbs.vue';
import MasterClassImage from '@/components/master-classes/MasterClassImage.vue';
import AvailablePlaces from '@/components/courses/AvailablePlaces.vue';
import LoadingSpinner from '@/components/common/LoadingSpinner.vue';

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
  <div class="min-h-screen bg-neutral-50">
    <Navbar />
    <Breadcrumbs :breadcrumbs="breadcrumbs" class="m-auto py-0.5 w-fit" />

    <!-- Состояние загрузки -->
    <div v-if="loading" class="container mx-auto px-4 py-8 text-center">
      <LoadingSpinner text="Загрузка мастер-классов..." color="red" size="md" />
    </div>

    <!-- Ошибка -->
    <div v-else-if="error" class="container mx-auto px-4 py-8 text-center">
      <p class="text-xl text-red-500">{{ error }}</p>
      <Button @click="fetchMasterClasses" class="mt-4">Повторить попытку</Button>
    </div>

    <!-- Список мастер-классов -->
    <div v-else class="container mx-auto">
      <Head title="Мастер-классы" />
      <!-- Заголовок -->
      <div class="text-center text-white bg-gray-700 mb-12 py-12">
        <h1 class="text-4xl md:text-5xl font-bold mb-4">
          Мастер-класс по рисованию в СПб:<br> живопись маслом
        </h1>
        <p class="text-3xl max-w-3xl mx-auto mb-5">
          Нарисуйте свою картину за 3 часа, даже если вы никогда не держали в руках кисть
        </p>
        <p class="text-xl">Холст, краски и кисти — всё включено</p>
        <p class="text-6xl font-bold py-3">2 900 ₽</p>
        <p class="text-sm">Заберёте готовую картину домой <br />
Студия у м. Звенигородская • Группы до 8 человек</p>
      </div>

      <!-- Список мастер-классов -->
      <div v-if="masterClasses.length > 0" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8 w-full lg:w-10/12 mx-auto">
        <div
          v-for="masterClass in masterClasses"
          :key="masterClass.id"
          class="bg-white lg:rounded-lg shadow-md overflow-hidden hover:shadow-lg transition-shadow duration-300 flex flex-col border border-neutral-200"
          style="height: 440px;"
        >
          <div class="flex flex-col h-full">
            <!-- Изображение мастер-класса -->
            <MasterClassImage
              :image-path="masterClass.image_path"
              :alt="masterClass.title"
            />

            <!-- Информация о мастер-классе -->
            <div class="p-6 flex flex-col flex-grow">
              <h2 class="text-xl font-semibold text-neutral-900 text-center">&laquo;{{ masterClass.title }}&raquo;</h2>
              <!-- Дата ближайшего события -->
                <div class="text-lg text-neutral-600 text-center">
                  {{ masterClass.formatted_next_event_date }}
                </div>
              <!-- Информация о местах -->
              <AvailablePlaces
                :max-participants="masterClass.max_participants"
                :booked-places="masterClass.booked_places"
              />

              <div class="mt-auto">
                <Link v-if="masterClass.max_participants - masterClass.booked_places > 0" :href="`/mk/${masterClass.slug}`">
                  <Button
                    class="w-full text-white text-xl lg:text-lg cursor-pointer bg-red-400 hover:bg-red-600"
                  >
                    Записаться
                  </Button>
                </Link>
                <Button
                  v-else
                  class="w-full text-white text-lg bg-gray-400 cursor-not-allowed"
                  disabled
                >
                  Мест нет
                </Button>
              </div>
            </div>
          </div>
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
