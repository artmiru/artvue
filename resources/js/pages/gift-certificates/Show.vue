<script setup lang="ts">
import { ref, onMounted, computed } from 'vue';
import { Head, Link } from '@inertiajs/vue3';
import Navbar from '@/components/Navbar.vue';
import { Button } from '@/components/ui/button';
import Breadcrumbs from '@/components/Breadcrumbs.vue';
import LoadingSpinner from '@/components/LoadingSpinner.vue';

// Интерфейс для сертификата
interface GiftCertificate {
  id: number;
  code: string;
  name: string;
  amount: number;
  visits_total: number;
  visits_used: number;
  expiry_date: string;
  purchaser_name: string;
  purchaser_phone: string | null;
  purchaser_email: string;
  payment_status: string;
  status: string;
  notes: string | null;
  created_at: string;
  updated_at: string;
}

// Данные сертификата
const certificate = ref<GiftCertificate | null>(null);
const loading = ref(true);
const error = ref<string | null>(null);

// Получение кода из URL
const props = defineProps({
  code: {
    type: String,
    required: true
  }
});

// Загрузка сертификата с API
const fetchCertificate = async () => {
  try {
    loading.value = true;
    error.value = null;

    const response = await fetch(`/api/gift-certificates`);
    const data: GiftCertificate[] = await response.json();

    // Найти сертификат по коду
    const foundCertificate = data.find(c => c.code === props.code);
    if (foundCertificate) {
      certificate.value = foundCertificate;
    } else {
      error.value = 'Сертификат не найден';
    }
  } catch (err) {
    console.error('Ошибка при загрузке сертификата:', err);
    error.value = 'Ошибка при загрузке сертификата';
  } finally {
    loading.value = false;
  }
};

// Загрузка сертификата при монтировании компонента
onMounted(() => {
  fetchCertificate();
});

// Хлебные крошки
const breadcrumbs = computed(() => [
  { title: 'Главная', href: '/' },
  { title: 'Сертификаты', href: '/gift-certificates' },
  { title: certificate.value?.name || 'Сертификат' }
]);

// Вычисляем количество оставшихся посещений
const remainingVisits = computed(() => {
  if (certificate.value) {
    return certificate.value.visits_total - certificate.value.visits_used;
  }
  return 0;
});

// Проверяем, истек ли сертификат
const isExpired = computed(() => {
  if (certificate.value) {
    const expiryDate = new Date(certificate.value.expiry_date);
    const currentDate = new Date();
    return expiryDate < currentDate;
  }
  return false;
});

// Форматируем дату истечения
const formattedExpiryDate = computed(() => {
  if (certificate.value) {
    const date = new Date(certificate.value.expiry_date);
    return date.toLocaleDateString('ru-RU', {
      year: 'numeric',
      month: 'long',
      day: 'numeric'
    });
  }
  return '';
});
</script>

<template>
  <div class="min-h-screen bg-background">
    <Navbar />
    <Breadcrumbs :breadcrumbs="breadcrumbs" class="mb-6 lg:w-fit lg:m-auto py-0.5" />
    
    <!-- Состояние загрузки -->
    <div v-if="loading" class="container mx-auto px-4 py-8 text-center">
      <LoadingSpinner text="Загрузка сертификата..." color="indigo" size="md" />
    </div>

    <!-- Ошибка -->
    <div v-else-if="error" class="container mx-auto px-4 py-8 text-center">
      <p class="text-xl text-red-500">{{ error }}</p>
      <Link href="/" class="text-blue-500 hover:underline mt-4 block">
        Вернуться на главную
      </Link>
    </div>

    <!-- Сертификат найден -->
    <div v-else-if="certificate" class="container mx-auto px-4 py-8">
      <Head :title="`Сертификат: ${certificate.name}`" />
      
      <!-- Заголовок -->
      <div class="text-center mb-12">
        <h1 class="text-4xl md:text-5xl font-bold text-neutral-900 mb-4">
          Подарочный сертификат
        </h1>
        <p class="text-xl text-neutral-700 max-w-3xl mx-auto">
          {{ certificate.name }}
        </p>
      </div>

      <!-- Основной контент сертификата -->
      <div class="grid grid-cols-1 lg:grid-cols-2 gap-12 mb-16">
        <!-- Визуальное представление сертификата -->
        <div class="bg-gradient-to-br from-blue-50 to-indigo-100 rounded-xl shadow-lg p-8 flex flex-col items-center justify-center">
          <div class="text-center mb-6">
            <h2 class="text-2xl font-bold text-indigo-80 mb-2">Подарочный сертификат</h2>
            <p class="text-lg text-indigo-600">{{ certificate.name }}</p>
          </div>
          
          <div class="bg-white rounded-lg shadow-md p-6 w-full max-w-md">
            <div class="text-center mb-4">
              <span class="text-4xl font-bold text-red-500">{{ (certificate.amount / 100).toFixed(2) }} ₽</span>
            </div>
            
            <div class="border-t border-b border-gray-200 py-4 my-4">
              <div class="flex justify-between mb-2">
                <span class="text-gray-600">Код сертификата:</span>
                <span class="font-mono font-bold">{{ certificate.code }}</span>
              </div>
              <div class="flex justify-between mb-2">
                <span class="text-gray-600">Истекает:</span>
                <span class="font-semibold">{{ formattedExpiryDate }}</span>
              </div>
              <div class="flex justify-between">
                <span class="text-gray-600">Посещений:</span>
                <span class="font-semibold">{{ certificate.visits_total }}</span>
              </div>
            </div>
            
            <div class="text-center">
              <p class="text-sm text-gray-500">Для активации предъявите этот сертификат в студии</p>
            </div>
          </div>
        </div>

        <!-- Информация о сертификате -->
        <div>
          <h2 class="text-3xl font-semibold text-neutral-900 mb-6">Информация о сертификате</h2>
          
          <div class="bg-neutral-50 p-6 rounded-lg mb-6">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-6">
              <div>
                <h3 class="text-lg font-semibold text-neutral-900 mb-2">Детали сертификата</h3>
                <ul class="space-y-2">
                  <li class="flex justify-between">
                    <span class="text-neutral-700">Номинал:</span>
                    <span class="font-semibold">{{ (certificate.amount / 100).toFixed(2) }} ₽</span>
                  </li>
                  <li class="flex justify-between">
                    <span class="text-neutral-700">Код:</span>
                    <span class="font-mono font-semibold">{{ certificate.code }}</span>
                  </li>
                  <li class="flex justify-between">
                    <span class="text-neutral-700">Посещений:</span>
                    <span class="font-semibold">{{ certificate.visits_total }}</span>
                  </li>
                  <li class="flex justify-between">
                    <span class="text-neutral-700">Использовано:</span>
                    <span class="font-semibold">{{ certificate.visits_used }}</span>
                  </li>
                  <li class="flex justify-between">
                    <span class="text-neutral-700">Осталось:</span>
                    <span class="font-semibold">{{ remainingVisits }}</span>
                  </li>
                </ul>
              </div>
              
              <div>
                <h3 class="text-lg font-semibold text-neutral-900 mb-2">Срок действия</h3>
                <ul class="space-y-2">
                  <li class="flex justify-between">
                    <span class="text-neutral-700">Истекает:</span>
                    <span class="font-semibold">{{ formattedExpiryDate }}</span>
                  </li>
                  <li class="flex justify-between">
                    <span class="text-neutral-700">Статус:</span>
                    <span :class="{
                      'text-green-600': certificate.status === 'active' && !isExpired,
                      'text-red-600': certificate.status === 'expired' || isExpired,
                      'text-yellow-600': certificate.status === 'used'
                    }" class="font-semibold">
                      {{ isExpired ? 'Истёк' : certificate.status === 'active' ? 'Активен' : certificate.status === 'used' ? 'Использован' : 'Неактивен' }}
                    </span>
                  </li>
                </ul>
              </div>
            </div>
            
            <Button 
              class="w-full bg-indigo-500 hover:bg-indigo-600 text-white text-lg py-3"
              :disabled="certificate.status !== 'active' || isExpired"
            >
              {{ certificate.status === 'active' && !isExpired ? 'Использовать сертификат' : isExpired ? 'Сертификат истёк' : 'Сертификат недоступен' }}
            </Button>
          </div>
          
          <!-- Информация о покупателе -->
          <div class="bg-white rounded-lg shadow-md p-6 mb-6">
            <h3 class="text-xl font-semibold text-neutral-900 mb-4">Информация о покупателе</h3>
            <p class="text-neutral-700 mb-2"><span class="font-semibold">Имя:</span> {{ certificate.purchaser_name }}</p>
            <p class="text-neutral-700 mb-2"><span class="font-semibold">Email:</span> {{ certificate.purchaser_email }}</p>
            <p v-if="certificate.purchaser_phone" class="text-neutral-700"><span class="font-semibold">Телефон:</span> {{ certificate.purchaser_phone }}</p>
          </div>
        </div>
      </div>

      <!-- Дополнительная информация -->
      <div class="bg-white rounded-lg shadow-md p-8 mb-12">
        <h3 class="text-2xl font-semibold text-neutral-900 mb-4">Условия использования</h3>
        <ul class="list-disc pl-5 space-y-2 text-neutral-700">
          <li>Сертификат действителен до {{ formattedExpiryDate }}</li>
          <li>Сертификат может быть использован для оплаты любых услуг студии</li>
          <li>Один сертификат может быть использован для нескольких посещений до исчерпания номинала</li>
          <li>Сертификат не подлежит обмену на денежные средства</li>
          <li>В случае истечения срока действия, сертификат может быть продлён по решению администрации</li>
        </ul>
      </div>
    </div>

    <!-- Сертификат не найден (запасной вариант) -->
    <div v-else class="container mx-auto px-4 py-8 text-center">
      <p class="text-xl text-neutral-700">Сертификат не найден</p>
      <Link href="/" class="text-blue-500 hover:underline mt-4 block">
        Вернуться на главную
      </Link>
    </div>
  </div>
</template>

<style scoped>
/* Дополнительные стили при необходимости */
</style>