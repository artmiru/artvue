<script setup lang="ts">
import { ref, onMounted } from 'vue';

// Props
const props = defineProps({
  imagePath: {
    type: String,
    required: true
  },
  alt: {
    type: String,
    default: ''
  }
});

// Reactive data
const isVertical = ref(false);
const imageLoaded = ref(false);

// Определяем ориентацию изображения динамически
const determineOrientation = (img: HTMLImageElement) => {
  // Если высота больше ширины, изображение вертикальное
  isVertical.value = img.naturalHeight > img.naturalWidth;
};

// Обработчик загрузки изображения
const handleImageLoad = (event: Event) => {
  const img = event.target as HTMLImageElement;
  determineOrientation(img);
  imageLoaded.value = true;
};

// Обработчик ошибки загрузки изображения
const handleImageError = () => {
  imageLoaded.value = true;
};
</script>

<template>
  <div
    class="aspect-w-4 aspect-h-3 overflow-hidden bg-gray-100 flex items-center justify-center"
    :class="{ 'bg-gray-50': !imageLoaded }"
  >
    <img
      :src="`/assets/img/mk/oil/thumbs/${imagePath}.webp`"
      :alt="alt"
      :class="[
        'transition-opacity duration-300',
        {
          'object-contain w-full h-full': isVertical,
          'object-cover object-center w-full h-full': !isVertical
        }
      ]"
      @load="handleImageLoad"
      @error="handleImageError"
    />
    <div v-if="!imageLoaded" class="w-full h-full flex items-center justify-center">
      <div class="animate-pulse bg-gray-200 rounded-full w-12 h-12"></div>
    </div>
  </div>
</template>

<style scoped>
/* Дополнительные стили при необходимости */
</style>