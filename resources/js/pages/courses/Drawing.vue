<script setup lang="ts">
import { ref } from 'vue';
import { Head } from '@inertiajs/vue3';
import { Button } from '@/components/ui/button';

// Основные темы курса
const courseTopics = ref([
  'Основы композиции',
  'Перспектива и пропорции',
  'Свет и тень',
  'Работа с различными материалами',
  'Портретное рисование',
  'Рисование натюрморта'
]);

// Преимущества обучения
const benefits = ref([
  'Индивидуальный подход к каждому студенту',
  'Опытные преподаватели-художники',
  'Практические занятия с натуры',
  'Развитие творческого мышления',
  'Создание портфолио работ',
  'Возможность участия в выставках'
]);

// Примеры работ студентов (заглушки)
const studentWorks = ref([
  { id: 1, title: 'Портрет карандашом', description: 'Работа студента 1 курса' },
  { id: 2, title: 'Натюрморт с фруктами', description: 'Работа студента 2 курса' },
  { id: 3, title: 'Пейзаж в технике графики', description: 'Работа студента 1 курса' }
]);
</script>

<template>
  <div class="min-h-screen bg-background">
    <Head title="Основы рисунка" />

    <div class="container mx-auto px-4 py-8">
      <!-- Заголовок и описание курса -->
      <div class="text-center mb-12">
        <h1 class="text-4xl md:text-5xl font-bold text-neutral-900 mb-6">Основы рисунка</h1>
        <p class="text-xl text-neutral-700 max-w-3xl mx-auto">
          Освойте фундаментальные навыки рисования под руководством опытных художников.
          Наш курс поможет вам развить художественное зрение и технические навыки.
        </p>
      </div>

      <!-- Список основных тем -->
      <div class="mb-16">
        <h2 class="text-3xl font-semibold text-neutral-900 mb-6 text-center">Что вы изучите</h2>
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 max-w-5xl mx-auto">
          <div
            v-for="(topic, index) in courseTopics"
            :key="index"
            class="bg-white p-6 rounded-lg shadow-md border border-neutral-200"
          >
            <div class="flex items-start">
              <div class="flex-shrink-0 h-6 w-6 rounded-full bg-red-500 flex items-center justify-center mr-4 mt-1">
                <span class="text-white text-sm font-bold">{{ index + 1 }}</span>
              </div>
              <h3 class="text-lg font-medium text-neutral-900">{{ topic }}</h3>
            </div>
          </div>
        </div>
      </div>

      <!-- Преимущества обучения -->
      <div class="mb-16 bg-neutral-50 py-12 rounded-lg">
        <div class="max-w-5xl mx-auto px-4">
          <h2 class="text-3xl font-semibold text-neutral-900 mb-6 text-center">Почему выбирают нас</h2>
          <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            <div
              v-for="(benefit, index) in benefits"
              :key="index"
              class="flex items-start"
            >
              <div class="flex-shrink-0 h-8 w-8 rounded-full bg-red-500 flex items-center justify-center mr-4 mt-1">
                <svg class="h-5 w-5 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                </svg>
              </div>
              <p class="text-lg text-neutral-700">{{ benefit }}</p>
            </div>
          </div>
        </div>
      </div>

      <!-- Примеры работ студентов -->
      <div class="mb-16">
        <h2 class="text-3xl font-semibold text-neutral-900 mb-6 text-center">Работы наших студентов</h2>
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8 max-w-5xl mx-auto">
          <div
            v-for="work in studentWorks"
            :key="work.id"
            class="bg-white rounded-lg shadow-md overflow-hidden border border-neutral-200"
          >
            <div class="bg-neutral-200 border-2 border-dashed rounded-xl w-full h-64" />
            <div class="p-6">
              <h3 class="text-xl font-semibold text-neutral-900 mb-2">{{ work.title }}</h3>
              <p class="text-neutral-600">{{ work.description }}</p>
            </div>
          </div>
        </div>
      </div>

      <!-- Призыв к действию -->
      <div class="text-center py-12">
        <h2 class="text-3xl font-semibold text-neutral-900 mb-6">Готовы начать свой творческий путь?</h2>
        <p class="text-xl text-neutral-700 mb-8 max-w-2xl mx-auto">
          Запишитесь на курс сейчас и получите скидку 10% на первый месяц обучения!
        </p>
        <Button class="bg-red-500 hover:bg-red-600 text-white text-lg py-3 px-8 rounded-lg">
          Записаться на курс
        </Button>
      </div>
    </div>
  </div>
</template>

<style scoped>
/* Дополнительные стили при необходимости */
</style>
