<script setup lang="ts">
interface Props {
  /**
   * Text to display below the spinner
   * @default 'Загрузка...'
   */
  text?: string;
  /**
   * Color of the spinner
   * @default 'blue'
   */
  color?: 'red' | 'green' | 'blue' | 'indigo' | 'purple' | 'gray';
  /**
   * Size of the spinner
   * @default 'md'
   */
  size?: 'sm' | 'md' | 'lg';
}

const props = withDefaults(defineProps<Props>(), {
  text: 'Загрузка...',
  color: 'blue',
  size: 'md'
});

// Map color names to Tailwind classes
const colorClasses = {
  red: 'border-red-500',
  green: 'border-green-500',
  blue: 'border-blue-500',
  indigo: 'border-indigo-500',
  purple: 'border-purple-500',
  gray: 'border-gray-500'
};

// Map size names to Tailwind classes
const sizeClasses = {
  sm: 'h-8 w-8',
  md: 'h-12 w-12',
  lg: 'h-16 w-16'
};
</script>

<template>
  <div class="flex flex-col items-center justify-center">
    <div 
      class="animate-spin rounded-full border-t-2 border-b-2"
      :class="[colorClasses[color], sizeClasses[size]]"
    ></div>
    <p 
      class="text-neutral-700 mt-2"
      :class="{
        'text-lg': size === 'sm',
        'text-xl': size === 'md',
        'text-2xl': size === 'lg'
      }"
    >
      {{ text }}
    </p>
  </div>
</template>

<style scoped>
/* Additional styles if needed */
</style>