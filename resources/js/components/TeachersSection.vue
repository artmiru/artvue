<script setup lang="ts">
// Интерфейс для учителя
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

// Props
const props = defineProps<{
  teachers?: Teacher[];
}>();

// Emits
const emit = defineEmits<{
  (e: 'view-teacher', teacher: Teacher): void;
}>();

// Функция для получения полного имени учителя
const getFullName = (teacher: Teacher): string => {
  const parts = [
    teacher.user.last_name,
    teacher.user.first_name
  ].filter(Boolean);

  return parts.join(' ') || 'Не указано';
};

// Функция для получения альтернативного текста
const getAltText = (teacher: Teacher): string => {
  if (teacher.alt) return teacher.alt;

  const fullName = getFullName(teacher);
  return `Преподаватель ${fullName} в студии рисования АртМир, СПб`;
};

// Функция для обработки ошибок загрузки изображений
const handleImageError = (event: Event) => {
  const target = event.target as HTMLImageElement;
  target.src = 'https://artmir.ru/assets/img/teacher/default.webp';
};

// Функция для обрезки текста до заданного количества символов
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

      <!-- Список учителей -->
      <div v-if="teachers && teachers.length > 0" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-2">
        <div
          v-for="teacher in teachers"
          :key="teacher.id"
          class="bg-white sm:rounded-xl overflow-hidden shadow-sm flex flex-col h-full pb-4"
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
          <div class="bg-white px-2 mt-auto">
            <button role="button"
              class="w-full py-1 font-medium bg-blue-400 hover:bg-blue-600 rounded-md text-white"
              @click="() => emit('view-teacher', teacher)"
            >
              Подробнее
            </button>
          </div>
        </div>
      </div>

      <!-- Пустой список -->
      <div v-else class="text-center py-12">
        <p class="text-gray-500 text-lg">Преподаватели пока не добавлены</p>
      </div>
    </div>
  </section>
</template>

<style scoped>
</style>
