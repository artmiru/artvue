<script setup lang="ts">
// Определение типов для курса
// Цена хранится в базе в копейках, но модель автоматически преобразует ее в рубли
interface Course {
  id: number
  name: string
  description: string
  image: string
  alt: string
  price: number // Цена в рублях (преобразуется из копеек моделью)
  slug: string
  href?: string // Добавляем необязательное поле href
}

// Определение типов для props
interface Props {
  courses: Course[]
}

// Получение props с валидацией
const props = withDefaults(defineProps<Props>(), {
  courses: () => []
})

// Вычисляемое свойство для URL курса
const getCourseUrl = (course: Course): string => {
  return course.href || (course.slug ? `${course.slug}` : '#')
}

// Форматирование цены
const formatPrice = (price: number): string => {
  return new Intl.NumberFormat('ru-RU', {
    style: 'currency',
    currency: 'RUB',
    minimumFractionDigits: 0,
    maximumFractionDigits: 0
  }).format(price)
}
</script>

<template>
    <div id="courses" class="max-w-full block md:mx-5 lg:mx-5">
      <div v-if="courses.length > 0" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-3">
        <div
          v-for="course in courses"
          :key="course.id"
          class="w-full flex"
        >
          <a
            :href="getCourseUrl(course)"
            class="block border rounded bg-white shadow-md flex flex-col w-full border-neutral-300"
          >
            <h2 class="pt-3 pb-1 text-2xl font-medium px-3 text-center">{{ course.name }}</h2>

            <div class="photo flex-grow">
              <img
                class="max-w-full h-auto w-full border-t border-b object-cover"
                :src="`/assets/img/main/${course.image}`"
                :alt="course.alt"
                loading="lazy"
                @error="($event) => ($event.target as HTMLImageElement).src = '/assets/img/main/placeholder.jpg'"
              >
              <div class="text text-lg leading-snug p-3 hidden">
                {{ course.description }}
              </div>
            </div>

            <div class="items-center flex justify-between p-3 pt-0 mt-auto">
              <div class="price inline-flex items-center relative -start-4">
                <div class="body bg-red-500 text-white relative text-lg py-1 flex items-center pl-9 pr-2">
                  {{ formatPrice(course.price) }}
                </div>
                <div class="arrow absolute block"></div>
              </div>

              <a
                v-if="course.slug"
                :href="`${course.slug}`"
                class="text-white text-base py-1 px-2 my-2 mr-2 w-1/2 inline-block text-center bg-red-500 rounded hover:bg-red-600"
              >
                Подробнее
              </a>
            </div>
          </a>
        </div>
      </div>

      <div v-else class="text-center py-8">
        <p class="text-gray-500 text-lg">Курсы временно недоступны. Пожалуйста, попробуйте позже.</p>
      </div>
    </div>
</template>
