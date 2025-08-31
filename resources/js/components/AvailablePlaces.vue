<script setup lang="ts">
import { computed } from 'vue';

// Props
const props = defineProps({
  maxParticipants: {
    type: Number,
    required: true
  },
  bookedPlaces: {
    type: Number,
    required: true
  }
});

// Вычисляем количество оставшихся мест
const availablePlaces = computed(() => {
  return props.maxParticipants - props.bookedPlaces;
});

// Определяем цвет border кружка в зависимости от количества мест
const circleBorderColor = computed(() => {
  if (availablePlaces.value <= 2) {
    return 'border-red-200';
  } else if (availablePlaces.value >= 3 && availablePlaces.value <= 4) {
    return 'border-yellow-200';
  } else {
    return 'border-lime-200';
  }
});

// Склонение слова "место" в зависимости от числа
const placesWord = computed(() => {
  const count = availablePlaces.value;
  const lastDigit = count % 10;
  const lastTwoDigits = count % 100;

  // Исключения для чисел 11-19
  if (lastTwoDigits >= 11 && lastTwoDigits <= 19) {
    return 'мест';
  }

  // Склонения для разных окончаний
  if (lastDigit === 1) {
    return 'место';
  } else if (lastDigit >= 2 && lastDigit <= 4) {
    return 'места';
  } else {
    return 'мест';
  }
});

// Проверяем, есть ли места
const hasPlaces = computed(() => {
  return availablePlaces.value > 0;
});
</script>

<template>
  <div class="mb-4 flex-grow">
    <div class="mb-1 text-center">
      <span v-if="hasPlaces" class="text-neutral-700">
        Осталось
        <span
          class="font-semibold border-4 rounded-full h-8 w-8 inline-flex text-xl justify-center items-center"
          :class="circleBorderColor"
        >
          {{ availablePlaces }}
        </span>
        {{ placesWord }}
      </span>
      <span v-else class="text-neutral-400 font-semibold block mt-1.5">
        Мест нет
      </span>
    </div>
  </div>
</template>

<style scoped>
/* Дополнительные стили при необходимости */
</style>
