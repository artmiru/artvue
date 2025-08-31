<script setup lang="ts">
import { ref, onMounted, computed } from 'vue';
import { Head, Link } from '@inertiajs/vue3';
import Navbar from '@/components/Navbar.vue';
import { Button } from '@/components/ui/button';
import Breadcrumbs from '@/components/Breadcrumbs.vue';

// Интерфейс для продукта
interface Product {
  id: number;
  title: string | null;
  type: 'trial' | 'regular' | 'masterclass' | 'material';
  price: number;
  warehouse_id: number | null;
  created_at: string;
  updated_at: string;
}

// Данные продукта
const product = ref<Product | null>(null);
const loading = ref(true);
const error = ref<string | null>(null);

// Получение ID из URL
const props = defineProps({
  id: {
    type: String,
    required: true
  }
});

// Загрузка продукта с API
const fetchProduct = async () => {
  try {
    loading.value = true;
    error.value = null;

    const response = await fetch(`/api/products`);
    const data: Product[] = await response.json();

    // Найти продукт по ID
    const foundProduct = data.find(p => p.id === parseInt(props.id));
    if (foundProduct) {
      product.value = foundProduct;
    } else {
      error.value = 'Продукт не найден';
    }
  } catch (err) {
    console.error('Ошибка при загрузке продукта:', err);
    error.value = 'Ошибка при загрузке продукта';
  } finally {
    loading.value = false;
  }
};

// Загрузка продукта при монтировании компонента
onMounted(() => {
  fetchProduct();
});

// Хлебные крошки
const breadcrumbs = computed(() => [
  { title: 'Главная', href: '/' },
  { title: 'Каталог', href: '/products' },
  { title: product.value?.title || 'Продукт' }
]);

// Получаем название типа продукта
const productTypeLabel = computed(() => {
  if (!product.value) return '';
  
  const typeLabels: Record<string, string> = {
    'trial': 'Пробное занятие',
    'regular': 'Регулярный продукт',
    'masterclass': 'Мастер-класс',
    'material': 'Материалы'
  };
  
  return typeLabels[product.value.type] || product.value.type;
});
</script>

<template>
  <div class="min-h-screen bg-background">
    <Navbar />
    <Breadcrumbs :breadcrumbs="breadcrumbs" class="mb-6 lg:w-fit lg:m-auto py-0.5" />
    
    <!-- Состояние загрузки -->
    <div v-if="loading" class="container mx-auto px-4 py-8 text-center">
      <p class="text-xl text-neutral-700">Загрузка продукта...</p>
    </div>

    <!-- Ошибка -->
    <div v-else-if="error" class="container mx-auto px-4 py-8 text-center">
      <p class="text-xl text-red-500">{{ error }}</p>
      <Link href="/" class="text-blue-500 hover:underline mt-4 block">
        Вернуться на главную
      </Link>
    </div>

    <!-- Продукт найден -->
    <div v-else-if="product" class="container mx-auto px-4 py-8">
      <Head :title="product.title || 'Продукт'" />
      
      <!-- Заголовок -->
      <div class="text-center mb-12">
        <h1 class="text-4xl md:text-5xl font-bold text-neutral-900 mb-4">
          {{ product.title || 'Продукт' }}
        </h1>
        <p class="text-xl text-neutral-700 max-w-3xl mx-auto">
          {{ productTypeLabel }}
        </p>
      </div>

      <!-- Основной контент продукта -->
      <div class="grid grid-cols-1 lg:grid-cols-2 gap-12 mb-16">
        <!-- Изображение продукта (заглушка) -->
        <div class="flex items-center justify-center bg-gray-100 rounded-lg shadow-md">
          <div class="text-center p-8">
            <div class="bg-gray-200 border-2 border-dashed rounded-xl w-64 h-64 mx-auto flex items-center justify-center">
              <span class="text-gray-500">Изображение продукта</span>
            </div>
            <p class="mt-4 text-neutral-700">Визуализация продукта будет здесь</p>
          </div>
        </div>

        <!-- Информация о продукте -->
        <div>
          <h2 class="text-3xl font-semibold text-neutral-900 mb-4">{{ product.title || 'Продукт' }}</h2>
          <p class="text-neutral-700 mb-6">{{ productTypeLabel }}</p>
          
          <div class="bg-neutral-50 p-6 rounded-lg mb-6">
            <div class="flex justify-between items-center mb-4">
              <span class="text-3xl font-bold text-green-500">{{ (product.price / 100).toFixed(2) }} ₽</span>
              <span class="text-neutral-500">цена</span>
            </div>
            
            <div class="mb-6">
              <h3 class="text-lg font-semibold text-neutral-900 mb-2">Детали продукта</h3>
              <ul class="space-y-2">
                <li class="flex justify-between">
                  <span class="text-neutral-700">Тип:</span>
                  <span class="font-semibold">{{ productTypeLabel }}</span>
                </li>
                <li class="flex justify-between">
                  <span class="text-neutral-700">ID:</span>
                  <span class="font-mono font-semibold">{{ product.id }}</span>
                </li>
                <li v-if="product.warehouse_id" class="flex justify-between">
                  <span class="text-neutral-700">Складской ID:</span>
                  <span class="font-mono font-semibold">{{ product.warehouse_id }}</span>
                </li>
              </ul>
            </div>
            
            <Button class="w-full bg-green-500 hover:bg-green-600 text-white text-lg py-3">
              Добавить в корзину
            </Button>
          </div>
          
          <!-- Описание -->
          <div class="bg-white rounded-lg shadow-md p-6">
            <h3 class="text-xl font-semibold text-neutral-900 mb-4">Описание</h3>
            <p class="text-neutral-700">
              Этот продукт предлагает отличное качество и подходит для различных нужд. 
              Приобретая этот продукт, вы получаете надежную и проверенную услугу.
            </p>
          </div>
        </div>
      </div>

      <!-- Дополнительная информация -->
      <div class="bg-white rounded-lg shadow-md p-8 mb-12">
        <h3 class="text-2xl font-semibold text-neutral-900 mb-4">Информация о продукте</h3>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
          <div>
            <h4 class="text-lg font-semibold text-neutral-900 mb-2">Характеристики</h4>
            <ul class="space-y-2">
              <li class="flex justify-between">
                <span class="text-neutral-700">Тип:</span>
                <span class="font-semibold">{{ productTypeLabel }}</span>
              </li>
              <li class="flex justify-between">
                <span class="text-neutral-700">Цена:</span>
                <span class="font-semibold">{{ (product.price / 100).toFixed(2) }} ₽</span>
              </li>
              <li v-if="product.warehouse_id" class="flex justify-between">
                <span class="text-neutral-700">Складской ID:</span>
                <span class="font-semibold">{{ product.warehouse_id }}</span>
              </li>
            </ul>
          </div>
          
          <div>
            <h4 class="text-lg font-semibold text-neutral-900 mb-2">Доставка и оплата</h4>
            <ul class="list-disc pl-5 space-y-2 text-neutral-700">
              <li>Доставка осуществляется в течение 3-5 рабочих дней</li>
              <li>Оплата возможна наличными или банковской картой</li>
              <li>Возможен самовывоз из студии</li>
              <li>При заказе от 5000 ₽ доставка бесплатная</li>
            </ul>
          </div>
        </div>
      </div>
    </div>

    <!-- Продукт не найден (запасной вариант) -->
    <div v-else class="container mx-auto px-4 py-8 text-center">
      <p class="text-xl text-neutral-700">Продукт не найден</p>
      <Link href="/" class="text-blue-500 hover:underline mt-4 block">
        Вернуться на главную
      </Link>
    </div>
  </div>
</template>

<style scoped>
/* Дополнительные стили при необходимости */
</style>