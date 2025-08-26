<script setup lang="ts">
// Определение типов для курса
interface Course {
  id: number
  name: string
  description: string
  image: string
  alt: string
  price: number
  slug: string
  href?: string // Добавляем необязательное поле href
}

// Определение типов для props
interface Props {
  courses: Course[]
}

// Получение props
defineProps<Props>()
</script>

<template>
  <section id="courses" class="pt-4 pb-5 bg-gray-100 border-b">
    <div class="container mx-auto max-w-screen-lg">
      <h1 class="text-center text-4xl mb-4 font-normal">
        Художественные курсы для взрослых <span class="whitespace-nowrap">в Санкт-Петербурге</span>
      </h1>

      <div class="flex flex-wrap text-center gap-2">
        <div v-for="course in courses" :key="course.id" class="sm:w-1/2 lg:w-4/12 p-1">
          <div class="border rounded bg-white" :data-href="course.href || '#'">
            <h3 class="pt-3 pb-1 text-lg">{{ course.name }}</h3>

            <div class="photo">
              <img 
                class="max-w-full h-auto w-full border-t border-b"
                :src="`/assets/img/main/${course.image}`"
                :alt="course.alt"
                loading="lazy"
              >
              <div class="text text-lg leading-snug">
                {{ course.description }}
              </div>
            </div>

            <div class="items-center flex justify-between">
              <div class="price-tag">
                {{ course.price }} ₽
              </div>

              <a 
                v-if="course.slug"
                :href="course.slug"
                class="border border-red-500 text-red-500 hover:bg-red-500 hover:text-white text-sm py-1 px-2 my-2 mr-2 w-1/2 inline-block text-center"
              >
                Подробнее
              </a>
            </div>
          </div>
        </div>
      </div>
    </div>
  </section>
</template>