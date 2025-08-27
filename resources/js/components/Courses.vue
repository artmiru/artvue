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
  loading?: boolean
}

// Получение props с валидацией
const props = withDefaults(defineProps<Props>(), {
  courses: () => [],
  loading: false
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
            class="border rounded bg-white shadow-md flex flex-col w-full border-neutral-300"
          >
            <h2 class="pt-3 pb-1 text-lg  md:text-xl lg:text-2xl font-medium px-3 text-center">{{ course.name }}</h2>

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
                <div class="body bg-red-500 text-white relative text-xl py-1 flex items-center pl-9 pr-2">
                  {{ formatPrice(course.price) }}
                </div>
                <div class="arrow absolute w-0 h-0 border-t-[19px] border-t-transparent border-b-[18px] border-b-transparent border-l-[14px] border-l-red-500 top-0 -right-[13px]"></div>
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

      <!-- Skeleton Loaders -->
      <div v-else-if="loading || (!loading && courses.length === 0)" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-3">
        <div
          v-for="i in 6"
          :key="i"
          class="w-full flex animate-pulse"
        >
          <div class="border rounded bg-white shadow-md flex flex-col w-full border-neutral-300">
            <div class="pt-3 pb-1 px-3">
              <div class="h-6 bg-gray-200 rounded w-3/4 mx-auto mb-2"></div>
              <div class="h-4 bg-gray-200 rounded w-1/2 mx-auto"></div>
            </div>

            <div class="photo flex-grow">
              <div class="bg-gray-200 border-t border-b w-full h-48"></div>
              <div class="p-3 space-y-2">
                <div class="h-4 bg-gray-200 rounded w-full"></div>
                <div class="h-4 bg-gray-200 rounded w-5/6"></div>
              </div>
            </div>

            <div class="flex justify-end p-3 pt-0 mt-auto">
                <div class="bg-gray-200 rounded w-1/3 h-8"></div>
            </div>
          </div>
        </div>
      </div>
    </div>
</template>
