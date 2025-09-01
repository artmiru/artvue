<script setup lang="ts">
// Teacher interface with detailed documentation
interface Teacher {
  id: number;
  user_id: number;
  about: string;
  phone: string;
  folder: string;
  alt: string | null;
  keypass_code: string;
  created_at: string;
  updated_at: string;
  deleted_at: string | null;
  user: {
    id: number;
    first_name: string;
    last_name: string | null;
    middle_name: string | null;
    phone: string;
    email: string | null;
    role: string;
    created_at: string;
    updated_at: string;
  };
}

// Define component props with detailed typing
const props = defineProps<{
  teachers?: Teacher[];
}>();

// Define component emits with detailed typing
const emit = defineEmits<{
  (e: 'view-teacher', teacher: Teacher): void;
}>();

// Computed property for teacher's full name to leverage Vue's reactivity caching
const getFullName = (teacher: Teacher): string => {
  const parts = [
    teacher.user.last_name,
    teacher.user.first_name
  ].filter(Boolean);

  return parts.join(' ') || 'Не указано';
};

// Computed property for image alt text to leverage Vue's reactivity caching
const getAltText = (teacher: Teacher): string => {
  if (teacher.alt) return teacher.alt;

  const fullName = getFullName(teacher);
  return `Преподаватель ${fullName} в студии рисования АртМир, СПб`;
};

// Function to handle image loading errors with improved fallback
const handleImageError = (event: Event) => {
  const target = event.target as HTMLImageElement;
  target.src = 'https://artmir.ru/assets/img/teacher/default.webp';
};

// Function to truncate text with better edge case handling
const truncateText = (text: string, maxLength: number = 90): string => {
  if (!text) return '';
  if (text.length <= maxLength) return text;
  return text.substring(0, maxLength) + '...';
};
</script>
<template>
  <section class="py-8 bg-gray-50 border-y border-gray-200">
    <div class="container mx-auto lg:max-w-6xl">
      <h2 class="text-center text-4xl font-bold mb-2 text-gray-800">Преподаватели</h2>
      <h3 class="text-gray-600 text-2xl font-normal text-center mb-8">Выпускники Императорской Академии художеств</h3>

      <!-- Teacher list grid with improved responsive design -->
      <div v-if="teachers && teachers.length > 0" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
        <div
          v-for="teacher in teachers"
          :key="teacher.id"
          class="bg-white sm:rounded-xl overflow-hidden shadow-sm flex flex-col h-full transition-all duration-200 hover:shadow-md"
        >
          <div class="relative">
            <img
              :src="`https://artmir.ru/assets/img/teacher/${teacher.folder}/01_t.webp`"
              :alt="getAltText(teacher)"
              class="w-full h-56 object-cover"
              loading="lazy"
              @error="handleImageError"
            >
          </div>
          <div class="p-5 flex flex-col flex-grow">
            <h3 class="text-xl font-semibold mb-2 leading-tight text-gray-800">
              {{ getFullName(teacher) }}
            </h3>
            <p class="text-sm text-gray-600 flex-grow">
              {{ truncateText(teacher.about) }}
            </p>
          </div>
          <div class="bg-white px-2 pb-4 mt-auto">
            <button
              class="w-full py-2 font-medium bg-blue-500 hover:bg-blue-600 rounded-md text-white transition-colors duration-200 focus:outline-none focus:ring-2 focus:ring-blue-300 focus:ring-offset-1"
              @click="() => emit('view-teacher', teacher)"
            >
              Подробнее
            </button>
          </div>
        </div>
      </div>

      <!-- Empty state with improved messaging -->
      <div v-else class="text-center py-16">
        <div class="inline-block p-4 bg-gray-100 rounded-full mb-4">
          <svg class="w-12 h-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 0 016 0zm6 3a2 2 0 1-4 0 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
          </svg>
        </div>
        <p class="text-gray-500 text-lg">Преподаватели пока не добавлены</p>
        <p class="text-gray-400 text-sm mt-2">Следите за обновлениями</p>
      </div>
    </div>
  </section>
</template>

<style scoped>
/* Added subtle transition effects for better user experience */
.transition-all {
  transition-property: all;
  transition-timing-function: cubic-bezier(0.4, 0, 0.2, 1);
  transition-duration: 150ms;
}
</style>
